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
    const vpnId = e.target.closest(".wppr-review-container").dataset.vpnId;
    const modalEl = createSubsModal(
      e.target.closest(".wppr-review-container"),
      getSubsHtml(vpnId)
    );
    modalEl.style.display = "";
    document.body.style.overflow = "hidden";
    setTimeout(() => {
      modalEl.style.opacity = 1;
    });
  }

  function createSubsModal(container, asyncContent) {
    const modalEl = container.querySelector(".subs-modal");

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
      () => (
        (modalEl.style.display = "none"), (document.body.style.overflow = "")
      ),
      {
        once: true,
      }
    );
  }

  async function fillSubsModal(modalEl, asyncContent) {
    const resultsEl = modalEl.querySelector(".subs-modal__results");
    const innerEl = modalEl.querySelector(".subs-modal__inner");
    const loadingEl = modalEl.querySelector(".subs-modal__loading");
    resultsEl.innerHTML = await asyncContent;
    loadingEl.style.display = "none";
    innerEl.style.display = "";
  }

  async function getSubsHtml(vpnId) {
    try {
      const sub = await getSub(vpnId);
      const inner = await generateSubList(sub);
      return inner;
    } catch (error) {
      return `<p class="subs-modal__error">${error.message}</p>`;
    }
  }

  async function generateSubList(sub) {
    const answers = prepareSubAnswers(sub.answers);

    return `
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
        return `<div class="subs-answer-list">${Object.values(answerObj.answer)
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
          ].includes(answer.type) &&
          !["yourvpn", "signature"].includes(answer.name)
      );
  }

  async function getSub(vpnId) {
    if (getSub.data) return getSub.data;
    try {
      const res = await fetch(
        `/wp-admin/admin-ajax.php?action=get_jotform_sub&id=${vpnId}`
      );
      const { success, data } = await res.json();
      if (success) return (getSub.data = data);
      else throw new Error(data);
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
