// Material's document$ emits on the first load and after every instant navigation.
document$.subscribe(() => {
    hljs.highlightAll();
});
