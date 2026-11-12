/* Pin Display */
const DIALOG = {
    el: document.getElementById("main-pin-dialog"),
    img: document.getElementById("pin-img"),
    caption: document.getElementById("pin-caption"),
    tags: document.getElementById("pin-tags"),
    permalink: document.getElementById("pin-permalink"),
    src: document.getElementById("pin-source")
};
const pinBTN = document.getElementsByClassName("pin-btn");
let current_pin;

for (const pin of pinBTN) {
    pin.addEventListener("click", (evt) => {
        current_pin = {
            id: evt.currentTarget.dataset.id,
            img: evt.currentTarget.dataset.img,
            src: evt.currentTarget.dataset.src,
            tags: evt.currentTarget.dataset.tags,
            tag_names: evt.currentTarget.dataset.names,
            caption: evt.currentTarget.dataset.caption,
            private: evt.currentTarget.dataset.private,
        };
    });
}

DIALOG.el.addEventListener("beforetoggle", (evt) => {
    if (evt.newState == "open") {

        DIALOG.img.src = current_pin.img;
        DIALOG.img.alt = "pin #" + current_pin.id;
        DIALOG.caption.innerHTML = current_pin.caption;
        DIALOG.permalink.href = "pin.php?p=" + current_pin.id;

        if (current_pin.src != "") {
            DIALOG.src.href = current_pin.src;
            DIALOG.src.classList.remove("hidden");
        };

        if (current_pin.tags != "") {
            let tags = current_pin.tags.split(" ");
            let tag_names = current_pin.tag_names.split("&&");

            for (let i = 0; i < tags.length; i++) {
                let li = document.createElement("li");
                let a = document.createElement("a");
                a.href = "tag.php?p=" + tags[i];
                a.innerHTML = tag_names[i];
                li.appendChild(a);
                DIALOG.tags.appendChild(li);
            }
        };

    } else {
        DIALOG.img.src = "";
        DIALOG.img.alt = "";
        DIALOG.caption.innerHTML = "";
        DIALOG.permalink.href = "";
        DIALOG.tags.innerHTML = "";
        DIALOG.src.href = "";
        DIALOG.src.classList.add("hidden");
    };
});
