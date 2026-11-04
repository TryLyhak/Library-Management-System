// Keeps track of the request that is currently loading,
// so a new click can cancel the old one
let currentNavigation = null;

// Returns true only for plain left-clicks on links to our own site
const isInternalGetLink = (event) => {
    const link = event.target.closest("a");

    if (!link || event.defaultPrevented || event.button !== 0) {
        return false;
    }

    // Let the browser handle Ctrl/Cmd-click (new tab), target="_blank" and downloads
    if (
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        link.target ||
        link.hasAttribute("download")
    ) {
        return false;
    }

    const url = new URL(link.href, window.location.origin);

    return (
        url.origin === window.location.origin &&
        url.protocol === window.location.protocol
    );
};

// Highlights the sidebar link that matches the current page
const syncSidebarLinks = () => {
    const currentPath = window.location.pathname;

    document.querySelectorAll("[data-sidebar-link]").forEach((link) => {
        const path = link.dataset.sidebarPath || "/";
        const isActive =
            path === "/"
                ? currentPath === "/"
                : currentPath === path || currentPath.startsWith(`${path}/`);

        link.classList.toggle("sidebar-link-active", isActive);
        link.classList.toggle("text-white/60", !isActive);

        if (isActive) {
            link.setAttribute("aria-current", "page");
        } else {
            link.removeAttribute("aria-current");
        }
    });
};

const dismissStatusAlert = (alert) => {
    alert.classList.add("is-dismissing");
    window.setTimeout(() => alert.remove(), 220);
};

const initializeStatusAlerts = () => {
    document.querySelectorAll("[data-status-alert]").forEach((alert) => {
        if (alert.dataset.statusInitialized) {
            return;
        }

        alert.dataset.statusInitialized = "true";
        alert.querySelector("[data-dismiss-alert]")?.addEventListener("click", () => {
            dismissStatusAlert(alert);
        });
        window.setTimeout(() => dismissStatusAlert(alert), 3000);
    });
};

// Loads a page in the background and swaps only the <main> content
const navigate = async (url, { replace = false } = {}) => {
    // Cancel the previous request, so an older response can't replace a newer page
    currentNavigation?.abort();
    const controller = new AbortController();
    currentNavigation = controller;

    document.documentElement.classList.add("is-navigating");

    try {
        const response = await fetch(url, {
            signal: controller.signal,
            headers: {
                Accept: "text/html",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        // The server redirected us (for example an expired session sent us to /login).
        // Do a normal full page load of the final URL, so the whole layout is correct.
        if (response.redirected) {
            window.location.assign(response.url);
            return;
        }

        if (!response.ok) {
            throw new Error(
                `Navigation failed with status ${response.status}.`,
            );
        }

        const html = await response.text();
        const nextDocument = new DOMParser().parseFromString(html, "text/html");
        const nextMain = nextDocument.querySelector("main");
        const currentMain = document.querySelector("main");

        if (!nextMain || !currentMain) {
            throw new Error(
                "Navigation response did not contain the expected page content.",
            );
        }

        // The sidebar exists only on logged-in pages. If the next page has a different
        // layout, swapping <main> alone would look broken, so do a full page load.
        const hasSidebar =
            document.querySelector("[data-sidebar-link]") !== null;
        const nextHasSidebar =
            nextDocument.querySelector("[data-sidebar-link]") !== null;

        if (hasSidebar !== nextHasSidebar) {
            window.location.assign(url);
            return;
        }

        if (replace) {
            window.history.replaceState({}, "", url);
        } else {
            window.history.pushState({}, "", url);
        }

        currentMain.replaceWith(nextMain);
        document.title = nextDocument.title;

        const currentWorkspaceTitle = document.querySelector(
            "[data-workspace-title]",
        );
        const nextWorkspaceTitle = nextDocument.querySelector(
            "[data-workspace-title]",
        );

        if (currentWorkspaceTitle && nextWorkspaceTitle) {
            currentWorkspaceTitle.textContent = nextWorkspaceTitle.textContent;
        }

        syncSidebarLinks();
        initializeStatusAlerts();
        window.scrollTo({ top: 0, behavior: "instant" });
    } catch (error) {
        // A newer click cancelled this request on purpose. That is not an error.
        if (error.name === "AbortError") {
            return;
        }

        console.error(error);
        window.location.assign(url);
    } finally {
        // Only the newest request is allowed to clear the loading state
        if (currentNavigation === controller) {
            currentNavigation = null;
            document.documentElement.classList.remove("is-navigating");
        }
    }
};

document.addEventListener("click", (event) => {
    if (!isInternalGetLink(event)) {
        return;
    }

    const link = event.target.closest("a");
    const url = new URL(link.href, window.location.origin);

    // Same page, nothing to load
    if (
        url.pathname === window.location.pathname &&
        url.search === window.location.search
    ) {
        return;
    }

    event.preventDefault();
    void navigate(url.href);
});

// Back and forward buttons
window.addEventListener("popstate", () => {
    void navigate(window.location.href, { replace: true });
});

const applyLiveFilter = (input) => {
    const table = document.getElementById(input.dataset.filterTarget);

    if (!table) {
        return;
    }

    const form = input.closest("form");
    const searchTerm =
        form?.querySelector("[data-live-filter-input]")?.value.trim().toLowerCase() ?? "";
    const categoryId =
        form?.querySelector("[data-live-filter-category]")?.value ?? "";
    const body = table.querySelector("tbody");

    if (!body) {
        return;
    }

    const rows = [...body.querySelectorAll("[data-filter-row]")];
    const matchingRows = rows.filter((row) => {
        const matchesText =
            searchTerm === "" || row.textContent.toLowerCase().includes(searchTerm);
        const matchesCategory =
            categoryId === "" || row.dataset.filterCategory === categoryId;
        const matches = matchesText && matchesCategory;
        row.style.display = matches ? "" : "none";

        return matches;
    });

    const emptyState = body.querySelector("[data-filter-empty]");
    if (emptyState) {
        const shouldShowEmptyState =
            (searchTerm !== "" || categoryId !== "") && matchingRows.length === 0;

        emptyState.hidden = !shouldShowEmptyState;
        emptyState.style.display = shouldShowEmptyState ? "table-row" : "none";
    }
};

document.addEventListener("input", (event) => {
    const input = event.target;

    if (input instanceof HTMLInputElement && input.matches("[data-live-filter-input]")) {
        applyLiveFilter(input);
    }
});

document.addEventListener("change", (event) => {
    const select = event.target;

    if (select instanceof HTMLSelectElement && select.matches("[data-live-filter-category]")) {
        applyLiveFilter(select);
    }
});

document.addEventListener("submit", (event) => {
    if (event.target.closest("[data-live-filter]")) {
        event.preventDefault();
    }

    if (event.target.matches("[data-confirm-form]") && !event.target.dataset.confirmed) {
        event.preventDefault();

        const dialog = document.getElementById("confirm-dialog");

        if (dialog) {
            dialog.dataset.formId = event.target.action;
            dialog.querySelector("#confirm-dialog-title").textContent =
                event.target.dataset.confirmTitle || "Please confirm";
            dialog.querySelector("#confirm-dialog-message").textContent =
                event.target.dataset.confirmMessage || "Are you sure you want to continue?";
            const confirmAction = dialog.querySelector("[data-confirm-action]");
            const confirmLabel = event.target.dataset.confirmLabel || "Delete";

            confirmAction.textContent = confirmLabel;
            confirmAction.classList.toggle("btn-primary", confirmLabel === "Return");
            confirmAction.classList.toggle("btn-danger", confirmLabel !== "Return");
            dialog.classList.remove("hidden");
            dialog.classList.add("flex");
            dialog.querySelector("[data-confirm-action]")?.focus();
        }
    }
});

document.querySelectorAll("[data-live-filter-input]").forEach((input) => {
    applyLiveFilter(input);
});

document.querySelectorAll("[data-live-filter-category]").forEach((select) => {
    applyLiveFilter(select);
});

initializeStatusAlerts();

const updateAutocomplete = (input) => {
    const optionsContainer = document.getElementById(input.dataset.autocompleteInput);

    if (!optionsContainer) {
        return;
    }

    const term = input.value.trim().toLowerCase();
    const options = [...optionsContainer.querySelectorAll("[data-autocomplete-option]")];

    if (term === "") {
        optionsContainer.classList.add("hidden");
        options.forEach((option) => option.classList.remove("hidden"));
        optionsContainer.querySelector("[data-autocomplete-empty]")?.classList.add("hidden");

        return;
    }

    const matches = options.filter((option) => {
        const matchesOption = term === "" || option.dataset.label.includes(term);
        option.classList.toggle("hidden", !matchesOption);

        return matchesOption;
    });

    optionsContainer.classList.remove("hidden");
    optionsContainer.querySelector("[data-autocomplete-empty]")?.classList.toggle("hidden", matches.length > 0);
};

document.addEventListener("input", (event) => {
    const input = event.target;

    if (input instanceof HTMLInputElement && input.matches("[data-autocomplete-input]")) {
        const hiddenInput = input.closest("[data-autocomplete]")?.querySelector("input[type='hidden']");

        if (hiddenInput) {
            hiddenInput.value = "";
        }

        if (input.id === "book_search" && input.value.trim() === "") {
            document.getElementById("book-availability")?.classList.add("hidden");
        }

        if (input.id === "member_search" && input.value.trim() === "") {
            document.getElementById("member-identity")?.classList.add("hidden");
        }

        updateAutocomplete(input);
    }
});

document.addEventListener("focusin", (event) => {
    const input = event.target;

    if (input instanceof HTMLInputElement && input.matches("[data-autocomplete-input]")) {
        updateAutocomplete(input);
    }
});

document.addEventListener("click", (event) => {
    const option = event.target.closest("[data-autocomplete-option]");

    if (option) {
        const container = option.closest("[data-autocomplete]");
        const input = container.querySelector("[data-autocomplete-input]");
        const hiddenInput = container.querySelector("input[type='hidden']");

        input.value = option.dataset.name || option.textContent.trim();
        hiddenInput.value = option.dataset.value;
        input.blur();
        option.parentElement.classList.add("hidden");

        if (input.id === "book_search") {
            const availability = document.getElementById("book-availability");
            const copies = Number(option.dataset.available);

            if (availability) {
                availability.textContent = `${copies} ${copies === 1 ? "copy" : "copies"} available to borrow`;
                availability.classList.remove("hidden");
            }
        }

        if (input.id === "member_search") {
            const identity = document.getElementById("member-identity");

            if (identity) {
                identity.textContent = `Member ID: #${option.dataset.memberCode}`;
                identity.classList.remove("hidden");
            }
        }

        return;
    }

    const closeConfirmButton = event.target.closest("[data-close-confirm-dialog]");
    const confirmDialog = document.getElementById("confirm-dialog");

    if (closeConfirmButton && confirmDialog) {
        confirmDialog.classList.add("hidden");
        confirmDialog.classList.remove("flex");
        return;
    }

    if (event.target.closest("[data-confirm-action]") && confirmDialog?.dataset.formId) {
        const form = document.querySelector(`[data-confirm-form][action="${CSS.escape(confirmDialog.dataset.formId)}"]`);

        if (form) {
            form.dataset.confirmed = "true";
            form.submit();
        }
    }
});

document.addEventListener("keydown", (event) => {
    const confirmDialog = document.getElementById("confirm-dialog");

    if (event.key === "Escape" && confirmDialog && !confirmDialog.classList.contains("hidden")) {
        confirmDialog.classList.add("hidden");
        confirmDialog.classList.remove("flex");
    }
});

syncSidebarLinks();
