//// Forms

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
