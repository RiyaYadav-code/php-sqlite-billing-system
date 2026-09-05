/* =====================================================
   MODULE NAVIGATION
   ===================================================== */

function showModule(section) {

    const validSections = [
        "dashboard",
        "billing",
        "products",
        "bills",
        "reports",
        "customers"
    ];


    /* Invalid section -> Dashboard */

    if (!validSections.includes(section)) {
        section = "dashboard";
    }


    /* Hide all modules */

    document
        .querySelectorAll(".module-section")
        .forEach(function(sectionElement) {

            sectionElement.classList.remove(
                "active-module"
            );

        });


    /* Show selected module */

    const selected =
        document.getElementById(section);

    if (selected) {

        selected.classList.add(
            "active-module"
        );

    }


    /* Update active navigation button */

    document
        .querySelectorAll(".module-link")
        .forEach(function(link) {

            link.classList.remove("active");

        });


    const activeLink =
        document.querySelector(
            '.module-link[data-section="' +
            section +
            '"]'
        );


    if (activeLink) {

        activeLink.classList.add("active");

    }


    /* Change URL without reloading */

    if (history.replaceState) {

        history.replaceState(
            null,
            "",
            "#" + section
        );

    }

}


/* Navigation click */

document
    .querySelectorAll(".module-link")
    .forEach(function(link) {

        link.addEventListener(
            "click",
            function(event) {

                event.preventDefault();

                showModule(
                    this.dataset.section
                );

            }
        );

    });


/* Open correct module when page loads */

const initialSection =
    document.body.dataset.activeSection ||
    (
        location.hash
            ? location.hash.substring(1)
            : "dashboard"
    );


showModule(initialSection);
