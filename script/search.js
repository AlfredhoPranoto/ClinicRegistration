const debug = async () => {
    let input = document.getElementById('search');

    try {
        const res = await fetch('history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `search=${encodeURIComponent(input.value)}`,
        });

        // Ambil teks dulu untuk lihat isinya
        const text = await res.text();
        console.log('Raw Response:', text);

        // Coba parse ke JSON
        const data = JSON.parse(text);
        console.log('Parsed JSON:', data);
        updateTable(data);
    } catch (error) {
        console.error('Error:', error);
    }
};

document.getElementById('search').addEventListener('input', debug);

const updateTable = (data) => {
    let tableBody = document.getElementById('table-body');
    tableBody.innerHTML = '';

    if (data.length === 0) {
        tableBody.innerHTML =
            "<tr><td colspan='15' class='text-center'>No results found</td></tr>";
        return;
    }

    data.forEach((patient) => {
        let row = `
          <tr>
              <td>${patient.id}</td>
              <td>${patient.waktu}</td>
              <td>${patient.perusahaan}</td>
              <td>${patient.dept}</td>
              <td>${patient.nama}</td>
              <td>${patient.lahir}</td>
              <td>${patient.berobat}</td>
              <td>${patient.diagnosa}</td>
              <td>${patient.obat}</td>
              <td>${patient.tindak}</td>
              <td>${patient.keterangan}</td>
              <td>${patient.dokter}</td>
              <td>${patient.keluhan}</td>
              <td class="text-center">
                    <a class="btn btn-primary fs-md fs-sm" href="./edit.php?id=${patient.id}">Edit</a>
                </td>
                <td class="text-center">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#deleteModal${patient.id}" class="btn btn-danger fs-md fs-sm delete-btn">
                        Delete
                    </button>
                </td>
          </tr>
      `;
        tableBody.innerHTML += row;
    });
};
