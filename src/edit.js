
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
