<!DOCTYPE html>
<html>
<head>
    <title>Добавление курьера Dostavista</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-top: 5px; }
        .success { color: green; }
        .section { border: 1px solid #eee; padding: 15px; margin-bottom: 20px; }
        .disabled { opacity: 0.6; pointer-events: none; }
        .required { color: red; margin-left: 3px; }
    </style>
</head>
<body>
    <h1>Добавление нового курьера</h1>
    <p>Поля, отмеченные <span class="required">*</span>, обязательны для заполнения</p>
    
    <form id="courierForm">
        <div class="section">
            <h3>Основные данные</h3>
            <div class="form-group">
                <label>Фамилия (surname)<span class="required">*</span>:</label>
                <input type="text" id="surname" required>
            </div>

            <div class="form-group">
                <label>Имя (name)<span class="required">*</span>:</label>
                <input type="text" id="name" required>
            </div>

            <div class="form-group">
                <label>Отчество (middlename):</label>
                <input type="text" id="middlename">
            </div>

            <div class="form-group">
                <label>Телефон (phone_number)<span class="required">*</span>:</label>
                <input type="tel" id="phone" pattern="\+7\d{10}" required placeholder="+79159972242">
            </div>
        </div>

        <div class="section">
            <h3>Дополнительная информация</h3>
            <div class="form-group">
                <label>Город (city)<span class="required">*</span>:</label>
                <input type="text" id="city" required>
            </div>

            <div class="form-group">
                <label>Транспорт (transport_type)<span class="required">*</span>:</label>
                <select id="transportType" required>
                    <option value="car">Автомобиль</option>
                    <option value="pedestrian">Пеший</option>
                </select>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="email" placeholder="courier@example.com">
            </div>

            <div class="form-group">
                <label>Дата рождения (birthday):</label>
                <input type="date" id="birthday">
            </div>

            <div class="form-group" id="driverLicenseGroup">
                <label>Номер в/у (driver_license_number):</label>
                <input type="text" id="driverLicense" placeholder="1234567890" disabled>
            </div>

            <div class="form-group" id="driverLicensePhotoGroup">
                <label>URL фото в/у (driver_license_photo_url):</label>
                <input type="url" id="driverLicensePhoto" placeholder="https://example.com/photo.jpg" disabled>
            </div>
        </div>

        <button type="submit">Добавить курьера</button>
    </form>

    <div id="response"></div>

    <script>
        const API_TOKEN = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD';
        const API_URL = 'http://koturaet.beget.tech/proxy.php';

        document.getElementById('transportType').addEventListener('change', function() {
            const isCarSelected = this.value === 'car';
            const driverLicense = document.getElementById('driverLicense');
            const driverLicensePhoto = document.getElementById('driverLicensePhoto');
            
            driverLicense.disabled = !isCarSelected;
            driverLicensePhoto.disabled = !isCarSelected;
            
            if (!isCarSelected) {
                driverLicense.value = '';
                driverLicensePhoto.value = '';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const transportSelect = document.getElementById('transportType');
            transportSelect.dispatchEvent(new Event('change'));
        });

        document.getElementById('courierForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const responseDiv = document.getElementById('response');
            responseDiv.innerHTML = '';
            
            const formData = {
                courier_partner_id: null,
                surname: document.getElementById('surname').value.trim(),
                name: document.getElementById('name').value.trim(),
                middlename: document.getElementById('middlename').value.trim() || null,
                phone_number: document.getElementById('phone').value.trim(),
                city: document.getElementById('city').value.trim(),
                transport_type: document.getElementById('transportType').value,
                email: document.getElementById('email').value.trim() || null,
                birthday: document.getElementById('birthday').value || null,
                driver_license_number: document.getElementById('driverLicense').value.trim() || null,
                driver_license_photo_url: document.getElementById('driverLicensePhoto').value.trim() || null
            };

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${API_TOKEN}`
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                let output = `<h3 class="${result.is_successful ? 'success' : 'error'}">Статус: ${
                    result.is_successful ? 'Успешно!' : 'Ошибка!'
                }</h3>`;
                
                if (result.errors) {
                    output += `<div class="error"><strong>Ошибки:</strong><ul>${
                        result.errors.map(e => `<li>${e}</li>`).join('')
                    }</ul></div>`;
                }

                if (result.parameter_errors) {
                    output += `<div class="error"><strong>Ошибки полей:</strong><ul>${
                        Object.entries(result.parameter_errors)
                            .map(([field, errors]) => 
                                `<li>${field}: ${errors.join(', ')}</li>`
                            ).join('')
                    }</ul></div>`;
                }

                if (result.courier) {
                    output += `<div class="success"><strong>Данные курьера:</strong><pre>${
                        JSON.stringify(result.courier, null, 2)
                    }</pre></div>`;
                }

                responseDiv.innerHTML = output;

                if (result.is_successful) {
                    document.getElementById('courierForm').reset();
                    document.getElementById('transportType').dispatchEvent(new Event('change'));
                }

            } catch (error) {
                let errorMessage = 'Ошибка сети: ';
                if (error.name === 'TypeError') {
                    errorMessage += 'Не удалось подключиться к серверу. Проверьте:';
                    errorMessage += '<ul>';
                    errorMessage += '<li>Интернет-соединение</li>';
                    errorMessage += '<li>Работу прокси-сервера</li>';
                    errorMessage += '<li>Корректность URL API</li>';
                    errorMessage += '</ul>';
                } else {
                    errorMessage += error.message;
                }
                
                responseDiv.innerHTML = `<div class="error"><strong>Критическая ошибка:</strong><br>${errorMessage}</div>`;
                console.error('Полная ошибка:', error);
            }
        });
    </script>
</body>
</html>