(function () {
  const URL_BASE = "/wp-json/wppr/v1/privacy";

  [...document.querySelectorAll(".review-wu-privacy-bar")].forEach((barEl) => {
    const trackerBtn = barEl.querySelector(".review-wu-privacy-trackers__more");
    const permissionBtn = barEl.querySelector(
      ".review-wu-privacy-permissions__more"
    );
    const versionBtn = barEl.querySelector(".review-wu-privacy-app__version");

    trackerBtn && trackerBtn.addEventListener("click", openTrackersModal);
    permissionBtn &&
      permissionBtn.addEventListener("click", openPermissionsModal);

    versionBtn && versionBtn.addEventListener("click", openVersionsModal);
  });

  /**
   *
   * @param {Event} e
   */
  function openReportDetailModal(e) {
    e.preventDefault();
    const { handle, versionCode, modalId, modalTitle, modalLoadingImg } =
      e.target.dataset;

    const openModal = createWuModal(
      modalId,
      modalTitle,
      "",
      modalLoadingImg,
      async () => getReportDetailModalContent(handle, versionCode)
    );

    openModal();
  }

  /**
   *
   * @param {Event} e
   */
  function openVersionsModal(e) {
    e.preventDefault();
    const { handle, modalId, modalTitle, modalLoadingImg } = e.target.dataset;

    const openModal = createWuModal(
      modalId,
      modalTitle,
      "",
      modalLoadingImg,
      async () => getVersionsModalContent(handle)
    );

    openModal();
  }

  /**
   *
   * @param {Event} e
   */
  function openPermissionsModal(e) {
    e.preventDefault();
    const { handle, versionCode, modalId, modalTitle, modalLoadingImg } =
      e.target.dataset;

    const openModal = createWuModal(
      modalId,
      modalTitle,
      "",
      modalLoadingImg,
      async () => getPermissionsModalContent(handle, versionCode)
    );

    openModal();
  }

  /**
   *
   * @param {Event} e
   */
  function openTrackersModal(e) {
    e.preventDefault();
    const { handle, versionCode, modalId, modalTitle, modalLoadingImg } =
      e.target.dataset;

    const openModal = createWuModal(
      modalId,
      modalTitle,
      "",
      modalLoadingImg,
      async () => getTrackersModalContent(handle, versionCode)
    );

    openModal();
  }

  async function getReportDetailModalContent(handle, versionCode) {
    try {
      const [report, grades] = await Promise.all([
        getReportDetail(handle, versionCode),
        getGrades(),
      ]);
      return getDetailReportModalContent(report, grades);
    } catch (error) {
      console.error(error);
    }
  }

  async function getVersionsModalContent(handle) {
    try {
      const [reports, grades] = await Promise.all([
        getReports(handle),
        getGrades(),
      ]);
      return generateVersionList(reports, grades);
    } catch (error) {
      console.error(error);
    }
  }

  async function getPermissionsModalContent(handle, versionCode) {
    try {
      const [report, grades] = await Promise.all([
        getReportDetail(handle, versionCode),
        getGrades(),
      ]);
      return {
        title: `
        <div class="privacy-report__title privacy-grade-el" style="margin-bottom: 0">
          <span class="privacy-grade-el__count privacy-grade-el__count--${calcGrade(
            report.permissions.length,
            grades
          )}">${report.permissions.length}</span>
          <span class="privacy-grade-el__title">${
            privacy_report_data.permissions
          }</span>
        </div>`,
        inner: `
        <div class="privacy-report">
          <div class="privacy-report__group">
            ${generatePermissionList(
              report.permissions,
              "privacy-report__list"
            )}
          </div>
        </div>`,
      };
    } catch (error) {
      console.error(error);
    }
  }

  async function getTrackersModalContent(handle, versionCode) {
    try {
      const [report, grades] = await Promise.all([
        getReportDetail(handle, versionCode),
        getGrades(),
      ]);
      return {
        title: `
        <div class="privacy-report__title privacy-grade-el" style="margin-bottom: 0">
          <span class="privacy-grade-el__count  privacy-grade-el__count--${calcGrade(
            report.trackers.length,
            grades
          )}">${report.trackers.length}</span>
          <span class="privacy-grade-el__title">${
            privacy_report_data.trackers
          }</span>
        </div>`,
        inner: `
        <div class="privacy-report">
          <div class="privacy-report__group">
            
            ${generateTrackerList(report.trackers, "privacy-report__list")}
          </div>
        </div>`,
      };
    } catch (error) {
      console.error(error);
    }
  }

  function getDetailReportModalContent(report, grades) {
    return `
    <div class="privacy-report">
      <div class="privacy-report__group">
        <div class="privacy-report__title privacy-grade-el">
            <span class="privacy-grade-el__count  privacy-grade-el__count--${calcGrade(
              report.trackers.length,
              grades
            )}">${report.trackers.length}</span>
            <span class="privacy-grade-el__title">${
              privacy_report_data.trackers
            }</span>
        </div>
        ${generateTrackerList(report.trackers, "privacy-report__list")}
      </div>
      <div class="privacy-report__group">
        <div class="privacy-report__title privacy-grade-el">
            <span class="privacy-grade-el__count privacy-grade-el__count--${calcGrade(
              report.permissions.length,
              grades
            )}">${report.permissions.length}</span>
            <span class="privacy-grade-el__title">${
              privacy_report_data.permissions
            }</span>
        </div>
        ${generatePermissionList(report.permissions, "privacy-report__list")}
      </div>
    </div>
    `;
  }

  function generateTrackerList(trackers, advClass) {
    return `<div class="privacy-tracker-list${advClass ? " " + advClass : ""}">
    ${trackers
      .map((tracker) => {
        return `
        <div class="privacy-tracker-el">
            <a href="${
              tracker.website
            }" class="privacy-tracker-el__title" rel="nofollow" target="_blank">
                <span>${tracker.name}</span>
                <img width="20" height="20" src="${
                  privacy_report_data.assest_folder
                }/img/right-arrow-round.svg">

            </a>
            <div class="privacy-tracker-el__footer privacy-category-list">
                ${tracker.categories
                  .map(
                    (category) => `
                    <div class="privacy-category-badge">
                        <div class="privacy-category-badge__title">
                            ${category}
                        </div>
                    </div>
                `
                  )
                  .join("")}
            </div>
        </div>
        `;
      })
      .join("")}
    </div>
    `;
  }

  function generatePermissionList(permissions, advClass) {
    return `<div class="privacy-permission-list${
      advClass ? " " + advClass : ""
    }">
    ${permissions
      .map((permission) => {
        return `
        <div class="privacy-permission-el">
          <div class="privacy-permission-el__top">
              ${
                permission.protection_level.includes("dangerous")
                  ? `<img class="privacy-permission-el__danger" width="8" src="${privacy_report_data.assest_folder}/img/exclamation-red.svg">`
                  : ""
              }
              <div class="privacy-permission-el__title">
                  ${permission["name"].split(".").reverse()[0]}
              </div>
            </div>
            ${
              permission.description
                ? `<div class="privacy-permission-el__desc">
                    ${permission.description}
                  </div>`
                : ""
            }
        </div>
        `;
      })
      .join("")}
    </div>
    `;
  }

  function generateVersionList(reports, grades) {
    return `<div class="privacy-version-list">
    ${reports
      .sort((a, b) => {
        return b.version_code - a.version_code;
      })
      .map((report) => {
        return `
        <div class="privacy-version-el">
            <div class="privacy-version-el__title">
                ${report.app_name}
            </div>
            <div class="privacy-version-el__version">
                ${report.version_name} - google
            </div>
            <div class="privacy-grade-list">
                <div class="privacy-grade-el">
                    <span class="privacy-grade-el__count privacy-grade-el__count--${calcGrade(
                      report.tracker_count,
                      grades
                    )}">${report.tracker_count}</span>
                    <span class="privacy-grade-el__title">${
                      privacy_report_data.trackers
                    }</span>
                </div>
                <div class="privacy-grade-el">
                    <span class="privacy-grade-el__count privacy-grade-el__count--${calcGrade(
                      report.permission_count,
                      grades
                    )}">${report.permission_count}</span>
                    <span class="privacy-grade-el__title">${
                      privacy_report_data.permissions
                    }</span>
                </div>
            </div>
        </div>
        `;
      })
      .join("")}
    </div>
    `;
  }

  async function getReports(handle) {
    try {
      if (getReports.history.has(handle)) return getReports.history.get(handle);
      const res = await fetch(`${URL_BASE}/reports/${handle}`, {
        headers: {
          "X-WP-Nonce": privacy_report_data.nonce,
        },
      });
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
      getReports.history.set(handle, rawData.data);
      return rawData.data;
    } catch (error) {
      console.error(error);
    }
  }
  getReports.history = new Map();

  async function getReportDetail(handle, versionCode) {
    try {
      if (getReportDetail.history.has(handle + versionCode))
        return getReportDetail.history.get(handle + versionCode);
      const res = await fetch(
        `${URL_BASE}/report/?handle=${handle}&version_code=${versionCode}`,
        {
          headers: {
            "X-WP-Nonce": privacy_report_data.nonce,
          },
        }
      );
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
      getReportDetail.history.set(handle + versionCode, rawData.data);
      return rawData.data;
    } catch (error) {
      console.error(error);
    }
  }
  getReportDetail.history = new Map();

  async function getGrades() {
    try {
      if (getGrades.grades) return getGrades.grades;

      const res = await fetch(`${URL_BASE}/grade`, {
        headers: {
          "X-WP-Nonce": privacy_report_data.nonce,
        },
      });
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
      getGrades.grades = rawData.data;
      return rawData.data;
    } catch (error) {
      console.error(error);
    }
  }

  function calcGrade(count, gradeMap) {
    if (count <= gradeMap.good_level.max_quantity) return "good";
    else if (count <= gradeMap.normal_level.max_quantity) return "normal";
    else return "bad";
  }
})();
