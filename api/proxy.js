export default async (req, res) => {
  // Обработка CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  const API_URL = 'https://robot.dostavista.ru/public/api/courier-partner/1.0/couriers';
  const API_TOKEN = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD';

  try {
    // Тело запроса уже парсится автоматически в Vercel
    const requestBody = req.body;

    const response = await fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`
      },
      body: JSON.stringify(requestBody)
    });

    const data = await response.json();
    res.status(response.status).json(data);

  } catch (error) {
    res.status(500).json({
      error: error.message,
      requestBody: req.body // Для отладки
    });
  }
};
