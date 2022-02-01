(function () {
  function init() {
    document.querySelectorAll(".review-wrap-up").forEach((reviewEl) => {
      initSubsModal(reviewEl);
    });
    document.querySelectorAll(".review-wu-left-mid").forEach((reviewEl) => {
      initReviewModal(reviewEl);
    });
  }

  function initSubsModal(reviewEl) {
    const link = reviewEl.querySelector(".cwpr-score-value a");
    if (!link) return;
    const allLink = document.querySelectorAll(
      `[href="${link.getAttribute("href")}"]`
    );
    allLink.forEach((link) => {
      link.addEventListener("click", openSubModal);
    });
  }

  function initReviewModal(reviewEl) {
    const link = reviewEl.querySelector(".review-wu-reviews-link");
    if (!link) return;
    link.addEventListener("click", openReviewModal);
  }

  async function openSubModal(e) {
    e.preventDefault();
    const vpnId = e.target.closest(".wppr-review-container").dataset.vpnId;
    const openModal = hydratateWuModal(
      document.querySelector(e.target.getAttribute("href")),
      async () => getSubsHtml(vpnId)
    );

    openModal();
  }

  async function openReviewModal(e) {
    e.preventDefault();
    const vpnId = e.target.closest(".wppr-review-container").dataset.vpnId;
    const openModal = hydratateWuModal(
      document.querySelector(e.currentTarget.getAttribute("href")),
      async () => getReviewsHtml(vpnId)
    );

    openModal();
  }

  async function getSubsHtml(vpnId) {
    try {
      const sub = await getSub(vpnId);
      const inner = await generateSubList(sub);
      return inner;
    } catch (error) {
      return `<p class="wu-modal__error">${error.message}</p>`;
    }
  }

  async function generateSubList(rawAnswers) {
    const answers = prepareSubAnswers(rawAnswers);

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

  async function getReviewsHtml(vpnId) {
    try {
      const sub = await getReviews(vpnId);
      const inner = await generateReviewList(sub);
      return inner;
    } catch (error) {
      return `<p class="wu-modal__error">${error.message}</p>`;
    }
  }

  async function getReviews(vpnId) {
    if (getReviews.data) return getReviews.data;
    try {
      const res = await fetch(
        `/wp-admin/admin-ajax.php?action=get_ext_reviews&id=${vpnId}`
      );
      const { success, data } = await res.json();
      if (success) return (getReviews.data = data);
      else throw new Error(data);
    } catch (error) {
      console.error(error.message);
      throw error;
    }
  }

  async function generateReviewList(data) {
    const intl = new Intl.NumberFormat("en-US", {
      style: "decimal",
      maximumFractionDigits: 1,
    });
    const intlVotes = new Intl.NumberFormat("en-US");
    return `<div class="wu-review-list wu-review-item--list">${data
      .map(getReviewEl)
      .join("")}</div>`;
  }

  function getReviewEl(el) {
    const intlVotes = new Intl.NumberFormat("en-US");
    const rawRating = el.rating;
    const rating = Math.round(rawRating);
    const rating5 = rating / 20;

    return `
    <a href="${
      el.url
    }" class="wu-review-item wu-review-item--starts" target="_blank" rel="nofollow">
                  <div class="wu-review-item__rating">
                    ${getStarRatingHtml(rawRating)}
                  </div>
                  <div class="wu-review-item__count">
                    ${rating5} / 5 (${intlVotes.format(el.votes)})
                  </div>
                  <div class="wu-review-item__title">
                    ${el.source}
                  </div>
                </a>
    `;
  }

  function getCircleRatingHtml(rawRating) {
    const intl = new Intl.NumberFormat("en-US", {
      style: "decimal",
      maximumFractionDigits: 1,
    });
    const rating = Math.round(rawRating);
    const rating5 = rating / 20;

    return `<div class="wppr-c100 wppr-p${rating} ${getRatingClass(rawRating)}">
              <span>${intl.format(rating5)}</span>
              <div class="wppr-slice">
                <div class="wppr-bar" style="transform: rotate(${
                  rating * 3.6
                }deg);">
                </div>
                <div class="wppr-fill"></div>
              </div>
              <div class="wppr-slice-center"></div>
            </div>`;
  }

  function getStarRatingHtml(rawRating) {
    return `
    <div class="wu-review-item__starts">
      <div class="wppr-review-stars wppr-review-stars--filled" style="width: ${rawRating}%">
        <i class="dashicons dashicons-star-filled wppr-dashicons"></i>
        <i class="dashicons dashicons-star-filled wppr-dashicons"></i>
        <i class="dashicons dashicons-star-filled wppr-dashicons"></i>
        <i class="dashicons dashicons-star-filled wppr-dashicons"></i>
        <i class="dashicons dashicons-star-filled wppr-dashicons"></i>
      </div>
      <div class="wppr-review-stars wppr-review-stars--empty">
        <i class="dashicons dashicons-star-empty wppr-dashicons"></i>
        <i class="dashicons dashicons-star-empty wppr-dashicons"></i>
        <i class="dashicons dashicons-star-empty wppr-dashicons"></i>
        <i class="dashicons dashicons-star-empty wppr-dashicons"></i>
        <i class="dashicons dashicons-star-empty wppr-dashicons"></i>
      </div>
    </div>
    `;
  }

  function getRatingClass(rating) {
    if (rating >= 75) {
      return "wppr-very-good";
    } else if (rating < 75 && rating >= 50) {
      return "wppr-good";
    } else if (rating < 50 && rating >= 25) {
      return "wppr-not-bad";
    } else {
      return "wppr-weak";
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
