document.addEventListener("DOMContentLoaded", () => {

    loadAdoptionData();

});


async function loadAdoptionData() {

    const tableBody =
        document.getElementById("requestTable");


    tableBody.innerHTML = `
        <tr>
            <td colspan="11" class="loading-data">
                Memuat data...
            </td>
        </tr>
    `;


    try {

        const response =
            await fetch("../api/adoption-list.php");


        if (!response.ok) {

            throw new Error(
                "Gagal mengambil data."
            );

        }


        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message ||
                "Data tidak dapat dimuat."
            );

        }


        displayAdoptionData(result.data);

        updateStatistics(result.data);


    } catch (error) {

        console.error(error);


        tableBody.innerHTML = `
            <tr>
                <td colspan="11" class="empty-data">
                    Data belum dapat dimuat.
                </td>
            </tr>
        `;

    }

}


function displayAdoptionData(data) {

    const tableBody =
        document.getElementById("requestTable");


    tableBody.innerHTML = "";


    if (!data || data.length === 0) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="11" class="empty-data">
                    Belum ada permintaan adopsi.
                </td>
            </tr>
        `;

        return;

    }


    data.forEach((item, index) => {

        const row =
            document.createElement("tr");


        row.innerHTML = `

            <td>
                ${index + 1}
            </td>


            <td>
                ${escapeHTML(item.ADOPTION_ID)}
            </td>


            <td>
                <strong>
                    ${escapeHTML(item.FULL_NAME)}
                </strong>
            </td>


            <td>
                ${escapeHTML(item.PET_NAME)}
            </td>


            <td>
                ${escapeHTML(item.EMAIL)}
            </td>


            <td>
                ${escapeHTML(item.PHONE)}
            </td>


            <td>
                ${escapeHTML(item.ADDRESS)}
            </td>


            <td>
                ${escapeHTML(item.REASON)}
            </td>


            <td>
                ${createStatusBadge(item.STATUS)}
            </td>


            <td>
                ${formatDate(item.CREATED_AT)}
            </td>


            <td>

                <select
                    class="action-select"
                    onchange="
                        changeStatus(
                            ${item.ADOPTION_ID},
                            this.value
                        )
                    "
                >

                    <option value="">
                        Pilih
                    </option>

                    <option value="Menunggu Review">
                        Menunggu Review
                    </option>

                    <option value="Approved">
                        Approved
                    </option>

                    <option value="Rejected">
                        Rejected
                    </option>

                </select>

            </td>

        `;


        tableBody.appendChild(row);

    });

}



function createStatusBadge(status) {

    if (status === "Approved") {

        return `
            <span class="status-badge status-approved">
                Approved
            </span>
        `;

    }


    if (status === "Rejected") {

        return `
            <span class="status-badge status-rejected">
                Rejected
            </span>
        `;

    }


    return `
        <span class="status-badge status-pending">
            Menunggu Review
        </span>
    `;

}


function updateStatistics(data) {

    let pending = 0;

    let approved = 0;

    let rejected = 0;


    data.forEach((item) => {

        if (item.STATUS === "Approved") {

            approved++;

        }

        else if (item.STATUS === "Rejected") {

            rejected++;

        }

        else {

            pending++;

        }

    });


    document.getElementById(
        "totalRequest"
    ).textContent = data.length;


    document.getElementById(
        "pendingRequest"
    ).textContent = pending;


    document.getElementById(
        "approvedRequest"
    ).textContent = approved;


    document.getElementById(
        "rejectedRequest"
    ).textContent = rejected;

}


async function changeStatus(id, status) {

    if (!status) {

        return;

    }


    const confirmation =
        confirm(
            `Ubah status pengajuan menjadi "${status}"?`
        );


    if (!confirmation) {

        loadAdoptionData();

        return;

    }


    try {

        const response =
            await fetch(
                "../api/adoption-status.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({
                        adoption_id: id,
                        status: status
                    })
                }
            );


        const result =
            await response.json();


        if (result.success) {

            alert(
                "Status berhasil diperbarui."
            );


            loadAdoptionData();

        }

        else {

            alert(
                result.message ||
                "Status gagal diperbarui."
            );


            loadAdoptionData();

        }


    } catch (error) {

        console.error(error);


        alert(
            "Tidak dapat terhubung ke server."
        );


        loadAdoptionData();

    }

}


function formatDate(dateValue) {

    if (!dateValue) {

        return "-";

    }


    const date =
        new Date(dateValue);


    if (isNaN(date.getTime())) {

        return dateValue;

    }


    return date.toLocaleDateString(
        "id-ID",
        {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        }
    );

}



function escapeHTML(value) {

    if (
        value === null ||
        value === undefined
    ) {

        return "";

    }


    return String(value)

        .replace(/&/g, "&amp;")

        .replace(/</g, "&lt;")

        .replace(/>/g, "&gt;")

        .replace(/"/g, "&quot;")

        .replace(/'/g, "&#039;");

}