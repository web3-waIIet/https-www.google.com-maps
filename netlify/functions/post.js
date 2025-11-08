export async function handler(event, context) {
  // CORS
  if (event.httpMethod === "OPTIONS") {
    return {
      statusCode: 200,
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Methods": "POST, OPTIONS",
        "Access-Control-Allow-Headers": "Content-Type",
      },
    };
  }

  if (event.httpMethod !== "POST") {
    return { statusCode: 405, body: JSON.stringify({status:"error", message:"Only POST allowed"}) };
  }

  const data = JSON.parse(event.body);
  const base64 = data.imageBase64?.replace(/^data:image\/\w+;base64,/, "");
  if (!base64) {
    return { statusCode: 400, body: JSON.stringify({status:"error", message:"No image sent"}) };
  }

  const filename = `capture_${Date.now()}.png`;

  return {
    statusCode: 200,
    headers: {"Access-Control-Allow-Origin": "*"},
    body: JSON.stringify({status:"success", file: filename}),
  };
}
