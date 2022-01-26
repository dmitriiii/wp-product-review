(function () {
  const URL_BASE = "/wp-json/wppr/v1/privacy";

  [...document.querySelectorAll(".review-wu-privacy-bar")].forEach((barEl) => {
    const trackerBtn = barEl.querySelector(".review-wu-privacy-trackers__more");
    const permissionBtn = barEl.querySelector(
      ".review-wu-privacy-permissions__more"
    );
    const versionBtn = barEl.querySelector(".review-wu-privacy-app__version");

    trackerBtn && trackerBtn.addEventListener("click", openReportDetailModal);
    permissionBtn &&
      permissionBtn.addEventListener("click", openReportDetailModal);

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

  async function getReportDetailModalContent(handle, versionCode) {
    try {
      const report = await getReportDetail(handle, versionCode);
      return (
        generateTrackerList(report.trackers) +
        generatePermissionList(report.permissions)
      );
    } catch (error) {}
  }

  async function getVersionsModalContent(handle) {
    try {
      const reports = await getReports(handle);
      const grades = await getGrades();
      return generateVersionList(reports, grades);
    } catch (error) {}
  }

  function generateTrackerList(trackers) {
    return `<div class="privacy-tracker-list">
    ${trackers
      .map((tracker) => {
        return `
        <div class="privacy-tracker-el">
            <div class="privacy-tracker-el__title">
                ${tracker.name}
            </div>
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

  function generatePermissionList(permissions) {
    return `<div class="privacy-permission-list">
    ${permissions
      .map((permission) => {
        return `
        <div class="privacy-permission-el">
            <div class="privacy-permission-el__title">
                ${permission.split(".").reverse()[0]}
            </div>
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
      .map((report) => {
        return `
        <div class="privacy-version-el">
            <div class="privacy-version-el__title">
                ${report.app_name}
            </div>
            <div class="privacy-version-el__version">
                ${report.version_name} - google
            </div>
            <div class="privacy-version-el__estimates">
                <div class="privacy-version-el__grade privacy-version-el__grade--${calcGrade(
                  report.tracker_count,
                  grades
                )}">
                    <span>${report.tracker_count}</span>
                    <span>trackers</span>
                </div>
                <div class="privacy-version-el__grade privacy-version-el__grade--${calcGrade(
                  report.permission_count,
                  grades
                )}">
                    <span>${report.permission_count}</span>
                    <span>permissions</span>
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
      const res = await fetch(`${URL_BASE}/reports/${handle}`);
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
      return rawData.data;
    } catch (error) {
      console.error(error);
    }
  }

  async function getReportDetail(handle, versionCode) {
    try {
      const res = await fetch(
        `${URL_BASE}/report/?handle=${handle}&version_code=${versionCode}`
      );
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
      return rawData.data;
    } catch (error) {
      console.error(error);
    }
  }

  async function getGrades() {
    try {
      const res = await fetch(`${URL_BASE}/grade`);
      const rawData = await res.json();
      if (res.status != 200) throw new Error(rawData.message);
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
