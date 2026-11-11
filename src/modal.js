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
                a.href = "pin.php?p=" + tags[i];
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

/* Forms */

// Image Drop

const dropZone = document.getElementById("drop-zone");

dropZone.addEventListener("drop", dropHandler);

window.addEventListener("drop", (e) => {
    if ([...e.dataTransfer.items].some((item) => item.kind === "file")) {
        e.preventDefault();
    }
});

dropZone.addEventListener("dragover", (e) => {
    const fileItems = [...e.dataTransfer.items].filter(
        (item) => item.kind === "file",
    );
    if (fileItems.length > 0) {
        e.preventDefault();
        if (fileItems.some((item) => item.type.startsWith("image/"))) {
            e.dataTransfer.dropEffect = "copy";
        } else {
            e.dataTransfer.dropEffect = "none";
        }
    }
});

window.addEventListener("dragover", (e) => {
    const fileItems = [...e.dataTransfer.items].filter(
        (item) => item.kind === "file",
    );
    if (fileItems.length > 0) {
        e.preventDefault();
        if (!dropZone.contains(e.target)) {
            e.dataTransfer.dropEffect = "none";
        }
    }
});

const fileInput = document.getElementById("file-input");

fileInput.addEventListener("change", (e) => {
    displayImage(e.target.files, null);
});

function dropHandler(ev) {
    ev.preventDefault();
    const files = [...ev.dataTransfer.items]
    .map((item) => item.getAsFile())
    .filter((file) => file);
    displayImage(files, null);
}

// Image URL

const imgURL = document.getElementById("url");

imgURL.addEventListener("change", () => {
    displayImage(null, imgURL.value);
});

// Image Preview

const preview = document.getElementById("image-preview");

function displayImage(files, url) {
    if (files != null) {
        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.addEventListener("load", () => {
            preview.src = reader.result;
            imgURL.value = reader.result;
        });

        reader.readAsDataURL(file);
    };

    if (url != null) {
        preview.src = url;
    };

    preview.classList.remove("hidden");
    dropZone.classList.add("hidden");
}

// Clear Image Preview

const clear = document.getElementById("clear");

clear.addEventListener("click", () => {
    preview.src = "";
    fileInput.value = null;
    imgURL.value = "";
    preview.classList.add("hidden");
    dropZone.classList.remove("hidden");
});

// Create Tags & Boards
const tagBtn = document.getElementById("add-tag-btn");
const boardBtn = document.getElementById("add-board-btn");

tagBtn.addEventListener("click", () => {
    let name = document.getElementById("add-tag");
    name = name.value.trim();
    if (name.length > 0) {
        createTag(name);
    }
});

boardBtn.addEventListener("click", () => {
    let title = document.getElementById("add-board");
    title = title.value.trim();
    if (title.length > 0) {
        createBoard(title);
    }
});

const tags = document.getElementById("tags");
const boards = document.getElementById("boards");

function createTag(name) {
    let option = document.createElement("option")
    option.value = "#" + name;
    option.innerHTML = name;
    tags.appendChild(option);
};

function createBoard(title) {
    let option = document.createElement("option")
    option.value = "#" + title;
    option.innerHTML = title;
    boards.appendChild(option);
};

/* Extension */
