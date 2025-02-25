export default async (req, res) => {
  // Настройка CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  // Обработка предварительного запроса
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  const API_URL = 'https://robot.dostavista.ru/public/api/courier-partner/1.0/couriers';
  const API_TOKEN = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD'; // ← Замените на свой ключ!

  try {
    // Логирование входящего запроса
    console.log('Received request:', {
      method: req.method,
      headers: req.headers,
      body: req.body
    });

    const response = await fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`
      },
      body: JSON.stringify(req.body)
    });

    // Логирование ответа от API
    console.log('API response:', {
      status: response.status,
      headers: Object.fromEntries(response.headers.entries())
    });

    const data = await response.json();
    
    res.status(response.status).json(data);

  } catch (error) {
    console.error('Error:', error);
    res.status(500).json({
      error: error.message,
      stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
    });
  }
};
