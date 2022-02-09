/**
 *
 * @param {string} id
 * @param {string} title
 * @param {string} subtitle
 * @param {string} loadingImg
 * @param {string|function} content
 * @returns
 */
function createWuModal(id, title, subtitle, loadingImg, content = "") {
  const tpl = getWuModalTpl(
    id,
    title,
    subtitle,
    loadingImg,
    typeof content === "function" ? "" : content
  );
  const template = document.createElement("template");
  template.innerHTML = tpl;
  const modalEl = template.content.querySelector(".wu-modal");
  modalEl.isCreate = true;

  document.body.append(template.content);

  return {
    modalEl,
    openModal: hydratateWuModal(
      modalEl,
      typeof content === "function" ? content : ""
    ),
    closeModal: () => closeWuModal(modalEl),
  };
}

function openWuModal(modalEl) {
  modalEl.style.display = "";
  document.body.style.overflow = "hidden";
  setTimeout(() => {
    modalEl.style.opacity = 1;
  });
}

function hydratateWuModal(modalEl, asyncContent = null) {
  initWuControl(modalEl);
  if (typeof asyncContent === "function") fillWuModal(modalEl, asyncContent);
  return () => openWuModal(modalEl);
}

function initWuControl(modalEl) {
  modalEl
    .querySelector(".wu-modal__close")
    .addEventListener("click", onCloseWuModal);
  modalEl.addEventListener("click", onCloseWuModal);
}

async function closeWuModal(modalEl) {
  modalEl.style.opacity = "";
  return new Promise((res) => {
    modalEl.addEventListener(
      "transitionend",
      () => {
        modalEl.style.display = "none";
        document.body.style.overflow = "";
        if (modalEl.isCreate) modalEl.remove();
        res();
      },
      {
        once: true,
      }
    );
  });
}

function onCloseWuModal(e) {
  if (
    !e.target.classList.contains("wu-modal__close") &&
    !e.target.classList.contains("wu-modal")
  )
    return;
  const modalEl = e.target.closest(".wu-modal");
  if (!modalEl) return;
  closeWuModal(modalEl);
}

async function fillWuModal(modalEl, asyncContent) {
  const resultsEl = modalEl.querySelector(".wu-modal__results");
  const titleEl = modalEl.querySelector(".wu-modal__title");
  const innerEl = modalEl.querySelector(".wu-modal__inner");
  const loadingEl = modalEl.querySelector(".wu-modal__loading");
  const content = await asyncContent();
  if (typeof content === "string") resultsEl.innerHTML = content;
  else {
    if (content.title) titleEl.innerHTML = content.title;
    if (content.inner) resultsEl.innerHTML = content.inner;
  }
  loadingEl.style.display = "none";
  innerEl.style.display = "";
}

function getWuModalTpl(
  id,
  title,
  subtitle = "",
  loadingImg = "",
  content = ""
) {
  return `<div id="${id}" class="wu-modal" style="display: none;">
      ${
        loadingImg
          ? `<div class="wu-modal__loading">
                <img alt="loading..." src="${loadingImg}">
            </div>`
          : ""
      }
  
      <div class="wu-modal__inner" style="display: none; max-width: 620px">
          <div role="button" class="wu-modal__close" title="close">
          </div>
          <div class="wu-modal__content">
              ${
                title
                  ? `
              <div class="wu-modal__title">
                  ${title}
              </div>`
                  : ""
              }
              ${
                subtitle
                  ? `
                <p class="wu-modal__subtitle">${subtitle}</p>
              `
                  : ""
              }
              <div class="wu-modal__results">
                  ${content}
              </div>
          </div>
      </div>
  </div>`;
}
