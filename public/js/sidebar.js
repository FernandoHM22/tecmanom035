async function initSidebar() {
  const initEvents = () => {
    $(document).on("click", "#btnMenu", function (e) {
      e.preventDefault();

      $("#sidebar").toggleClass("collapsed");
      $("#navbar-top").toggleClass("collapsed");
      $("#main-content").toggleClass("collapsed");

      const isCollapsed = $("#sidebar").hasClass("collapsed");
      localStorage.setItem("sidebarCollapsed", isCollapsed ? "1" : "0");

      // Cambia el ícono
      const icon = $("#iconMenu");
      icon.removeClass("fa-bars fa-x");
      icon.addClass(isCollapsed ? "fa-x" : "fa-bars");
    });

    // $(document).on("mouseenter", "#btnMenu", function () {
    //   $(this).trigger("click");
    // });
  };

  const applySidebarState = () => {
    const isCollapsed = localStorage.getItem("sidebarCollapsed") === "1";
    if (isCollapsed) {
      $("#sidebar").addClass("collapsed");
      $("#navbar-top").addClass("collapsed");
      $("#main-content").addClass("collapsed");
      $("#iconMenu").removeClass("fa-bars").addClass("fa-x");
    } else {
      $("#iconMenu").removeClass("fa-x").addClass("fa-bars");
    }
  };

  const markActiveLink = () => {
    const currentPath = window.location.pathname;
    const currentSegment = getLastSegmentPage(currentPath);

    $("#sidebar a").each(function () {
      const href = $(this).attr("href");
      const hrefSegment = getLastSegmentPage(href);
      if (currentSegment === hrefSegment) {
        $(this).addClass("active");
      }
    });
  };

  const getLastSegmentPage = (url) => {
    return url.split("/").filter(Boolean).pop();
  };

  try {
    applySidebarState();
    initEvents();
    markActiveLink();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}
