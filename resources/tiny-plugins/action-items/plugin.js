(function () {
    const uuid = () =>
        "ai-" +
        ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
            (
                c ^
                (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
            ).toString(16),
        );

    tinymce.PluginManager.add("action-items", function (editor) {
        const liSel = "li[data-ai-id]";

        const insertChecklist = () => {
            const listId = uuid();
            const itemId = uuid();
            editor.insertContent(
                `<ul class="ai-checklist" data-ai-list-id="${listId}">
          <li data-ai-id="${itemId}" data-ai-checked="false" role="checkbox" aria-checked="false" tabindex="0">
            <span class="ai-item">
              <input type="checkbox" aria-hidden="true" contenteditable="false">
              <span class="ai-text">New action item</span>
            </span>
          </li>
        </ul>`,
            );
        };

        const setChecked = (li, checked) => {
            li.setAttribute("data-ai-checked", String(checked));
            li.setAttribute("aria-checked", String(checked));
            const cb = li.querySelector('input[type="checkbox"]');
            if (cb) {
                cb.checked = checked;
            }
            editor.undoManager.add();
        };
        const toggle = (li) =>
            setChecked(li, li.getAttribute("data-ai-checked") !== "true");

        // Normalize
        const normalize = () => {
            editor
                .getBody()
                .querySelectorAll(liSel)
                .forEach((li) => {
                    // label -> span
                    const oldLabel = li.querySelector("label.ai-item");
                    if (oldLabel) {
                        const span = editor.getDoc().createElement("span");
                        span.className = "ai-item";
                        while (oldLabel.firstChild) {
                            span.appendChild(oldLabel.firstChild);
                        }
                        oldLabel.replaceWith(span);
                    }
                    const itemWrap = li.querySelector(".ai-item") || li;

                    // checkbox
                    let cb = li.querySelector('input[type="checkbox"]');
                    if (!cb) {
                        cb = editor.getDoc().createElement("input");
                        cb.type = "checkbox";
                        cb.setAttribute("aria-hidden", "true");
                        cb.setAttribute("contenteditable", "false");
                        itemWrap.insertBefore(cb, itemWrap.firstChild);
                    } else {
                        cb.setAttribute("contenteditable", "false");
                    }

                    // text wrapper
                    let text = itemWrap.querySelector(".ai-text");
                    if (!text) {
                        text = editor.getDoc().createElement("span");
                        text.className = "ai-text";
                        // Move all siblings except the checkbox into .ai-text
                        const nodesToMove = [];
                        itemWrap.childNodes.forEach((n) => {
                            if (n !== cb) {
                                nodesToMove.push(n);
                            }
                        });
                        nodesToMove.forEach((n) => text.appendChild(n));
                        itemWrap.appendChild(text);
                    }

                    // sync checked attrs
                    const checked =
                        li.getAttribute("data-ai-checked") === "true";
                    cb.checked = checked;

                    li.setAttribute("role", "checkbox");
                    li.setAttribute("aria-checked", String(checked));
                    li.setAttribute("tabindex", "0");
                });
        };

        editor.on("LoadContent SetContent", normalize);
        editor.on("keyup", (e) => {
            if (e.key === "Enter") {
                normalize();
            }
        });

        // Mouse: toggle on checkbox click
        editor.on("mousedown", (e) => {
            const cb =
                e.target &&
                e.target.closest &&
                e.target.closest('input[type="checkbox"]');
            if (!cb) {
                return;
            }
            const li = cb.closest(liSel);
            if (!li || !editor.getBody().contains(li)) {
                return;
            }
            e.preventDefault();
            toggle(li);
        });

        // Let Enter split lines naturally. (No handler for Space.)
        editor.on("keydown", (e) => {
            if (e.key === "Enter") {
                // default TinyMCE behavior; checkbox for new LI will be added in keyup->normalize
            }
        });

        editor.ui.registry.addButton("checklist", {
            text: "Checklist",
            tooltip: "Insert checklist",
            onAction: insertChecklist,
        });

        return { getMetadata: () => ({ name: "Action Items" }) };
    });
})();
