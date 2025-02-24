export default async (req, res) => {
  // Обработка CORS для предварительных запросов
  if (req.method === 'OPTIONS') {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    return res.status(200).end();
  }

  const API_URL = 'https://robot.dostavista.ru/public/api/courier-partner/1.0/couriers';
  const API_TOKEN = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD';

  try {
    // Получаем тело запроса из req.body
    const requestBody = JSON.parse(req.body);

    const response = await fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`
      },
      body: JSON.stringify(requestBody)
    });

    const data = await response.json();
    
    // Устанавливаем CORS-заголовки
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.status(response.status).json(data);

  } catch (error) {
    res.status(500).json({ 
      error: error.message,
      stack: error.stack 
    });
  }
};
