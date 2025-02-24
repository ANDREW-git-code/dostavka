export default async (req, res) => {
  const API_URL = 'https://robot.dostavista.ru/public/api/courier-partner/1.0/couriers';
  const API_TOKEN = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD'; // Ваш ключ!
  
  try {
    const response = await fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`
      },
      body: JSON.stringify(await req.json())
    });
    
    const data = await response.json();
    
    res.status(response.status).json({
      ...data,
      // Добавляем CORS-заголовки
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Methods': 'POST'
      }
    });

  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
