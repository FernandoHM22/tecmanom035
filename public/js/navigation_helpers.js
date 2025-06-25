function goTo(relativePath) {
  const basePath = document.body.getAttribute("data-basepath") || "";
  const normalizedPath = "/" + relativePath.replace(/^\/+/, "");
  const fullUrl = basePath + normalizedPath;
  window.location.href = fullUrl;
}
