<?php

function after_filter_codeBlocks($data, $folder) {
    // Convert indented code blocks to proper markdown code fences
    // BUT: Be conservative and only convert content that's clearly code
    // Skip admonition blocks, navigation lists, and other structured content

    $lines = explode("\n", $data);
    $result = [];
    $inCodeBlock = false;
    $inAdmonition = false;
    $codeBlockLanguage = '';
    $codeBlockLines = [];
    $baseIndent = 0;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $isIndented = preg_match('/^( {4,})(.*)$/', $line, $matches);
        $isEmpty = trim($line) === '';
        $isAdmonitionStart = preg_match('/^!!! (note|warning|important|tip|danger|info|success|error)/', $line);

        // Check if we're starting an admonition block
        if ($isAdmonitionStart) {
            $inAdmonition = true;
            $result[] = $line;
            continue;
        }

        // Check if we're ending an admonition block (non-indented line after admonition)
        if ($inAdmonition && !$isIndented && !$isEmpty) {
            $inAdmonition = false;
        }

        // Skip processing indented lines if we're inside an admonition
        if ($inAdmonition) {
            $result[] = $line;
            continue;
        }

        if ($isIndented && !$inCodeBlock) {
            $content = $matches[2];

            // Only convert to code block if content clearly looks like code
            // Skip navigation lists, bullet points, and other structured content
            $looksLikeCode = false;

            // Strong indicators this is code:
            if (preg_match('/^<(?:!doctype|html|head|body|title|div|span|p|h[1-6])/i', $content)) {
                $looksLikeCode = true;
                $codeBlockLanguage = 'html';
            } elseif (preg_match('/^<\?php|function\s+\w+|class\s+\w+|\$\w+\s*=|public\s+function|private\s+function|protected\s+function/', $content)) {
                $looksLikeCode = true;
                $codeBlockLanguage = 'php';
            } elseif (preg_match('/^(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)\s+/i', $content)) {
                $looksLikeCode = true;
                $codeBlockLanguage = 'sql';
            } elseif (preg_match('/^(console|bash|\$)\s/', $content)) {
                $looksLikeCode = true;
                $codeBlockLanguage = 'console';
            } elseif (preg_match('/^\w+\s*\{|\w+\s*\(.*\)\s*\{|import\s+|from\s+\w+\s+import/', $content)) {
                $looksLikeCode = true;
                $codeBlockLanguage = '';
            }

            // Skip if it looks like a navigation list or other structured content
            if (preg_match('/^[-*+]\s+|^\d+\.\s+|^[A-Za-z\s]+:$/', $content)) {
                $looksLikeCode = false;
            }

            if ($looksLikeCode) {
                // Starting a new code block
                $inCodeBlock = true;
                $codeBlockLines = [];
                $baseIndent = strlen($matches[1]); // Remember the base indentation level

                $result[] = '```' . $codeBlockLanguage;
                $codeBlockLines[] = $content;
            } else {
                // Regular indented content - preserve as-is
                $result[] = $line;
            }
        } elseif ($isIndented && $inCodeBlock) {
            // Continue the code block, preserving relative indentation
            $currentIndent = strlen($matches[1]);
            $relativeIndent = max(0, $currentIndent - $baseIndent);
            $preservedContent = str_repeat(' ', $relativeIndent) . $matches[2];
            $codeBlockLines[] = $preservedContent;
        } elseif ($isEmpty && $inCodeBlock) {
            // Empty line within code block - add it to maintain spacing
            $codeBlockLines[] = '';
        } elseif (!$isIndented && !$isEmpty && $inCodeBlock) {
            // End the code block
            $result = array_merge($result, $codeBlockLines);
            $result[] = '```';
            $result[] = '';
            $inCodeBlock = false;
            $baseIndent = 0;

            // Add the current non-indented line
            $result[] = $line;
        } else {
            // Regular line - not part of a code block
            if (!$inCodeBlock) {
                $result[] = $line;
            }
        }
    }

    // If we ended while still in a code block, close it
    if ($inCodeBlock) {
        $result = array_merge($result, $codeBlockLines);
        $result[] = '```';
    }

    return implode("\n", $result);
}
