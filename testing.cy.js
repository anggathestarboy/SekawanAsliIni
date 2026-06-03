describe('End-to-End API Testing Suite for Amanah Elektronik', () => {
    // Ubah sesuai dengan port server lokal Laravel Anda (biasanya 8000)
    const apiUrl = 'http://localhost:8000/api';
    
    let authToken = '';
    
    // Test data IDs
    let kategoriId = '';
    let alatId = '';
    let pelangganId = '';
    let pelangganDataId = '';
    let penyewaanId = '';

    // Menghasilkan data acak agar test selalu berhasil walau dijalankan berulang (username unik)
    const uniqueSuffix = Date.now();
    const adminData = {
        admin_username: `admin_test_${uniqueSuffix}`,
        admin_password: 'password123'
    };

    it('1. Should Register a new Admin', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/auth/register`,
            body: adminData
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    it('2. Should Login and get Bearer Token', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/auth/login`,
            body: adminData
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            expect(response.body.token).to.exist;
            
            // Simpan token untuk dipakai di request berikutnya
            authToken = response.body.token; 
        });
    });

    // --- CRUD KATEGORI ---
    it('3. Should Create Kategori', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/kategori`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: { kategori_nama: 'Kategori Test Cypress' }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            kategoriId = response.body.data.kategori_id;
        });
    });

    it('4. Should Read All Kategori', () => {
        cy.request({
            method: 'GET',
            url: `${apiUrl}/kategori`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            expect(response.body.data).to.be.an('array');
        });
    });

    it('5. Should Update Kategori', () => {
        cy.request({
            method: 'PUT',
            url: `${apiUrl}/kategori/${kategoriId}`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: { kategori_nama: 'Kategori Test Cypress Updated' }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            expect(response.body.data.kategori_nama).to.eq('Kategori Test Cypress Updated');
        });
    });

    // --- CRUD ALAT ---
    it('6. Should Create Alat', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/alat`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: {
                alat_kategori_id: kategoriId,
                alat_nama: 'Alat Test Cypress',
                alat_deskripsi: 'Deskripsi alat testing',
                alat_hargaperhari: 50000,
                alat_stok: 10
            }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            alatId = response.body.data.alat_id;
        });
    });

    it('7. Should Update Alat', () => {
        cy.request({
            method: 'PUT',
            url: `${apiUrl}/alat/${alatId}`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: {
                alat_kategori_id: kategoriId,
                alat_nama: 'Alat Test Cypress Updated',
                alat_deskripsi: 'Deskripsi update',
                alat_hargaperhari: 60000,
                alat_stok: 15 // Naikkan stok
            }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    // --- CRUD PELANGGAN ---
    it('8. Should Create Pelanggan', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/pelanggan`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: {
                pelanggan_nama: 'Pelanggan Cypress',
                pelanggan_alamat: 'Jalan Cypress No 1',
                pelanggan_notelp: '081234567890',
                pelanggan_email: `cypress_${uniqueSuffix}@test.com`
            }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            pelangganId = response.body.data.pelanggan_id;
        });
    });

    // --- CRUD PELANGGAN DATA (FILE UPLOAD) ---
    it('9. Should Create Pelanggan Data (with Mock File)', () => {
        // cy.request() default tidak mendukung FormData multipart/form-data.
        // Solusinya adalah memanggil XMLHttpRequest secara native di dalam window browser:
        const blob = Cypress.Blob.base64StringToBlob('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=', 'image/png');
        const formData = new FormData();
        formData.append('pelanggan_data_pelanggan_id', pelangganId);
        formData.append('pelanggan_data_jenis', 'KTP');
        formData.append('pelanggan_data_file', blob, 'test.png');

        cy.window().then(win => {
            return new Cypress.Promise((resolve, reject) => {
                const xhr = new win.XMLHttpRequest();
                xhr.open('POST', `${apiUrl}/pelanggan_data`);
                xhr.setRequestHeader('Authorization', `Bearer ${authToken}`);
                xhr.onload = () => resolve(JSON.parse(xhr.responseText));
                xhr.onerror = () => reject(xhr.statusText);
                xhr.send(formData);
            });
        }).then((response) => {
            expect(response.success).to.be.true;
            pelangganDataId = response.data.pelanggan_data_id;
        });
    });

    // --- CRUD PENYEWAAN ---
    it('10. Should Create Penyewaan (Transaction DB)', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/penyewaan`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: {
                penyewaan_pelanggan_id: pelangganId,
                penyewaan_tglsewa: '2026-06-03',
                penyewaan_tglkembali: '2026-06-05',
                penyewaan_sttspembayaran: 'DP',
                penyewaan_sttskembali: 'Belum Kembali',
                details: [
                    {
                        penyewaan_detail_alat_id: alatId,
                        penyewaan_detail_jumlah: 2
                    }
                ]
            }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            penyewaanId = response.body.data.penyewaan_id;
        });
    });

    it('11. Should Update Status Penyewaan (Dikembalikan)', () => {
        cy.request({
            method: 'PUT',
            url: `${apiUrl}/penyewaan/${penyewaanId}`,
            headers: { Authorization: `Bearer ${authToken}` },
            body: {
                penyewaan_sttspembayaran: 'Lunas',
                penyewaan_sttskembali: 'Sudah Kembali' // Ini akan otomatis mengembalikan / rollback stok!
            }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
            expect(response.body.data.penyewaan_sttskembali).to.eq('Sudah Kembali');
        });
    });

    // --- TEARDOWN (HAPUS SEMUA DATA DEMI CLEAN DATABASE) ---
    // Hapus dari hirarki paling bawah agar tidak kena Constraint Error
    
    it('12. Should Delete Penyewaan', () => {
        cy.request({
            method: 'DELETE',
            url: `${apiUrl}/penyewaan/${penyewaanId}`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    it('13. Should Delete Pelanggan Data', () => {
        cy.request({
            method: 'DELETE',
            url: `${apiUrl}/pelanggan_data/${pelangganDataId}`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    it('14. Should Delete Pelanggan', () => {
        cy.request({
            method: 'DELETE',
            url: `${apiUrl}/pelanggan/${pelangganId}`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    it('15. Should Delete Alat', () => {
        cy.request({
            method: 'DELETE',
            url: `${apiUrl}/alat/${alatId}`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    it('16. Should Delete Kategori', () => {
        cy.request({
            method: 'DELETE',
            url: `${apiUrl}/kategori/${kategoriId}`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });

    // --- LOGOUT TERAKHIR ---
    it('17. Should Logout Successfully', () => {
        cy.request({
            method: 'POST',
            url: `${apiUrl}/auth/logout`,
            headers: { Authorization: `Bearer ${authToken}` }
        }).then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.success).to.be.true;
        });
    });
});
