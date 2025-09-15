# CLI Overview

CodeIgniter 4 provides built-in command **spark** and useful commands and library. You can also create spark commands, and run controllers via CLI.

- [What is the CLI?](#what-is-the-cli)
- [Why Run via the Command-Line?](#why-run-via-the-command-line)
- [The Spark Commands](#the-spark-commands)
- [The CLI Library](#the-cli-library)

## What is the CLI?

The command-line interface is a text-based method of interacting with computers. For more information, check the <a href="https://en.wikipedia.org/wiki/Command-line_interface" target="_blank">Wikipedia article</a>.

## Why Run via the Command-Line?

There are many reasons for running CodeIgniter from the command-line, but they are not always obvious.

- Run your cron-jobs without needing to use *wget* or *curl*.

- Make interactive "tasks" that can do things like set permissions, prune cache folders, run backups, etc.

- Integrate with other applications in other languages. For example, a random C++ script could call one command and run code in your models!

## The Spark Commands

CodeIgniter ships with the official command **spark** and built-in commands.

You can run the spark and see the help:

``` console
php spark
```

See the [Spark Commands](spark_commands.md) page for detailed information.

## The CLI Library

The CLI library makes working with the CLI interface simple. It provides easy ways to output text in multiple colors to the terminal window. It also allows you to prompt a user for information, making it easy to build flexible, smart tools.

See the [CLI Library](#/cli/cli-library) page for detailed information.
