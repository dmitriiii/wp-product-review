(function () {
  function init() {
    document.querySelectorAll(".review-wrap-up").forEach((reviewEl) => {
      initSubsModal(reviewEl);
    });
  }

  async function initSubsModal(reviewEl) {
    const link = reviewEl.querySelector(".cwpr-score-value a");
    if (!link) return;
    link.addEventListener("click", openSubModal);
  }

  async function openSubModal(e) {
    e.preventDefault();
    const subId = e.target.closest(".wppr-review-container").dataset.subId;
    const modalEl = createSubsModal(getSubsHtml(subId));
    document.body.append(modalEl);
    document.body.style.overflow = "hidden";
    setTimeout(() => {
      modalEl.style.opacity = 1;
    });
  }

  function createSubsModal(asyncContent) {
    const modalEl = document.createElement("div");
    modalEl.classList.add("subs-modal");
    modalEl.innerHTML = `
    <div class="subs-modal__loading">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255, 0); display: block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
          <g transform="rotate(0 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(30 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(60 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(90 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(120 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(150 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(180 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(210 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(240 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(270 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(300 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
            </rect>
          </g><g transform="rotate(330 50 50)">
            <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
              <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
            </rect>
          </g>
        </svg>
      </div>
    <div class="subs-modal__inner" style="display: none">
      <div role="button" class="subs-modal__close" title="close">
      </div>
      <div class="subs-modal__content">
      </div>
    </div>
    `;

    initSubsControl(modalEl);
    fillSubsModal(modalEl, asyncContent);
    return modalEl;
  }

  function initSubsControl(modalEl) {
    modalEl
      .querySelector(".subs-modal__close")
      .addEventListener("click", closeSubsModal);
    modalEl.addEventListener("click", closeSubsModal);
  }

  function closeSubsModal(e) {
    if (
      !e.target.classList.contains("subs-modal__close") &&
      !e.target.classList.contains("subs-modal")
    )
      return;
    const modalEl = e.target.closest(".subs-modal");

    modalEl.style.opacity = "";
    modalEl.addEventListener(
      "transitionend",
      () => (modalEl.remove(), (document.body.style.overflow = ""))
    );
  }

  async function fillSubsModal(modalEl, asyncContent) {
    const contentEl = modalEl.querySelector(".subs-modal__content");
    const innerEl = modalEl.querySelector(".subs-modal__inner");
    const loadingEl = modalEl.querySelector(".subs-modal__loading");
    contentEl.innerHTML = await asyncContent;
    loadingEl.style.display = "none";
    innerEl.style.display = "";
  }

  async function getSubsHtml(subId) {
    const sub = await getSub(subId);
    const inner = await generateSubList(sub);
    return inner;
  }

  async function generateSubList(sub) {
    const answers = prepareSubAnswers(sub.answers);
   
    return `
    <h2 class="subs-modal__title">VPN Trust-Level</h2>
    <div class="subs-list">
      ${answers
        .map(
          (answer) => `
      <div class="subs-el">
        <div class="subs-el__title">${answer.text}</div>
        <div class="subs-el__answer">${toUrl(prepareAnswer(answer))}</div>
      </div>`
        )
        .join("")}
    </div>
    `;
  }

  function prepareAnswer(answerObj) {
    if (!answerObj.answer) return "–";
    switch (answerObj.type) {
      case "control_textbox":
        return answerObj.answer;
      case "control_address":
        const keys = Object.keys(answerObj.answer);
        const line1 = keys
          .filter((key) => key.includes("addr_line"))
          .map((key) => answerObj.answer[key])
          .join(", ");
        const line2 = [answerObj.answer.city, answerObj.answer.postal]
          .filter((val) => val)
          .join(", ");
        const line3 = answerObj.answer.country;
        return [line1, line2, line3].filter((line) => line).join("<br>");
      case "control_radio":
        const mod =
          answerObj.answer === "Yes" ||
          answerObj.answer === "No" ||
          answerObj.answer === "Partly" ||
          answerObj.answer === "None"
            ? answerObj.answer.toLocaleLowerCase()
            : "txt";
        return `<div class="subs-answer-box subs-answer-box--${mod}">
        ${answerObj.answer}
        </div>`;
      case "control_number":
        return answerObj.answer;
      case "control_spinner":
        return answerObj.answer;
      case "control_email":
        return answerObj.answer;
      case "control_checkbox":
        return `<div class="subs-answer-list">${answerObj.answer
          .map(
            (answer) => `<div class="subs-answer-box subs-answer-box--nth">
        ${answer}
        </div>`
          )
          .join("")}</div>`;
      case "control_textarea":
        return answerObj.answer;
      case "control_datetime":
        const date = new Date(
          Date.UTC(
            +answerObj.answer.year,
            +answerObj.answer.month,
            +answerObj.answer.day
          )
        );
        return new Intl.DateTimeFormat("en-US", {
          month: "short",
          year: "numeric",
          day: "numeric",
        }).format(date);
      case "control_signature":
        return `<img alt="signature" src="${answerObj.answer}">`;

      default:
        return answerObj.answer;
    }
  }

  function prepareSubAnswers(answers) {
    return Object.values(answers)
      .sort((a, b) => {
        return +a.order < +b.order;
      })
      .filter(
        (answer) =>
          ![
            "control_divider",
            "control_image",
            "control_pagebreak",
            "control_head",
            "control_button",
            "control_text",
            "control_widget",
          ].includes(answer.type) && !["yourvpn", "signature"].includes(answer.name)
      );
  }

  async function getSub(subId) {
    if (getSub.data) return getSub.data;
    try {
      const res = await fetch(
        `/wp-admin/admin-ajax.php?action=get_jotform_sub&id=${subId}`
      );
      const { success, data } = await res.json();
      if (success) return (getSub.data = data);
    } catch (error) {
      console.error(error.message);
      throw error;
    }
  }

  function toUrl(string) {
    let url;

    try {
      url = new URL(string);
    } catch (err) {
      return string;
    }

    return `<a href=${url} target="_blank" rel="nofollow">${string}</a>`;
  }

  init();
})();
