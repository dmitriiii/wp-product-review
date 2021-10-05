(function () {
  function init() {
    document.querySelectorAll(".review-wrap-up").forEach((reviewEl) => {
      initSubsModal(reviewEl);
    });
  }

  async function initSubsModal(reviewEl) {
    const link = reviewEl.querySelector(".cwpr-score-value");
    if (!link) return;
    link.addEventListener("click", openSubModal);
  }

  async function openSubModal(e) {
    e.preventDefault();
    const subId = e.target.closest(".wppr-review-container").dataset.subId;
    const modal = await createSubModal(subId);
  }

  async function createSubModal(subId) {
    const sub = await getSub(subId);
    const inner = await generateSubList(sub);
    document.querySelector('.subs-modal').innerHTML = inner;
  }

  async function generateSubList(sub) {
    const answers = prepareSubAnswers(sub.answers);
    return `
    <div class="subs-list">
      ${answers.map(
        (answer) => `
      <div class="subs-el">
        <div class="subs-el__title">${answer.text}</div>
        <div>${prepareAnswer(answer)}</div>
      </div>`
      ).join()}
    </div>
    `;
  }

  function prepareAnswer(answerObj) {
    if (!answerObj.answer) return "-";
    switch (answerObj.type) {
      case "control_textbox":
        return answerObj.answer;
      case "control_address":
        return answerObj.prettyFormat;
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
        return `<div>${answerObj.answer
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
            "control_widget"
          ].includes(answer.type) && !["yourvpn"].includes(answer.name)
      );
  }

  async function getSub(subId) {
    try {
      const res = await fetch(
        `/wp-admin/admin-ajax.php?action=get_jotform_sub&id=${subId}`
      );
      const { success, data } = await res.json();
      if (success) return data;
    } catch (error) {
      console.error(error.message);
      throw error;
    }
  }

  init();
})();
