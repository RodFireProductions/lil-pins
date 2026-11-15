
const edit = document.getElementById("edit");
const info = document.getElementById("info");
const form = document.getElementById("edit-form");

let editing = false;
edit.addEventListener("click", (evt) => {
    let which = evt.currentTarget.dataset.which;

    if (editing) {
        info.classList.remove("hidden");
        form.classList.add("hidden");
        editing = false;
    } else {
        info.classList.add("hidden");
        form.classList.remove("hidden");
        editing = true;
    };
});

// Create Tags & Boards
const tagBtn2 = document.getElementById("add-tag-btn2");
const boardBtn2 = document.getElementById("add-board-btn2");

tagBtn2.addEventListener("click", () => {
    let name = document.getElementById("add-tag2");
    name = name.value.trim();
    if (name.length > 0) {
        createTag2(name);
    }
});

boardBtn2.addEventListener("click", () => {
    let title = document.getElementById("add-board2");
    title = title.value.trim();
    if (title.length > 0) {
        createBoard2(title);
    }
});

const tags2 = document.getElementById("tags2");
const boards2 = document.getElementById("boards2");

function createTag2(name) {
    let option = document.createElement("option")
    option.value = "#" + name;
    option.innerHTML = name;
    tags2.appendChild(option);
};

function createBoard2(title) {
    let option = document.createElement("option")
    option.value = "#" + title;
    option.innerHTML = title;
    boards2.appendChild(option);
};
