import "bootstrap"

import '../scss/app.scss';

const addLikeFetch = (url: string, node: HTMLElement) => {
    fetch(url, {
        method: "POST"
    }).then((response) => {
        if (!response.ok) {
            throw "Network error";
        }
        return response.json();
    }).then((data) => {
        if (data.likes == null) {
            return;
        }
        node.innerHTML = data.likes;
    }).catch((err) => {
        console.error(err);
    });
};

window.addEventListener("load", (e) => {

    e.preventDefault();

    const jsLikeArticles = <HTMLElement[]>Array.from(
        document.getElementsByClassName("js-like-article")
    );

    jsLikeArticles.forEach((node) => {
        const url = node.dataset.url;
        if (url == null) {
            return;
        }
        const parent = node.parentElement;
        if (parent == null) {
            return;
        }
        const numberNode = <HTMLElement>(
            parent.getElementsByClassName("js-like-article-count")[0]
        );
        node.addEventListener('click', () => addLikeFetch(url, numberNode));
    });
});
