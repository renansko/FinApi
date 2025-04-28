from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse
from io import BytesIO

from PIL import Image                         # abrir e pré-processar imagens :contentReference[oaicite:8]{index=8}
from pyzbar.pyzbar import decode               # decodificar QR Codes via ZBar :contentReference[oaicite:9]{index=9}

app = FastAPI()

@app.post("/decode-qr/")
async def decode_qr(file: UploadFile = File(...)):
    # 1. Validação de formato
    if not file.filename.lower().endswith((".png", ".jpg", ".jpeg")):
        raise HTTPException(status_code=400, detail="Formato de imagem inválido")

    # 2. Leitura em memória e abertura com Pillow
    data = await file.read()
    try:
        image = Image.open(BytesIO(data))
    except Exception:
        raise HTTPException(status_code=422, detail="Não foi possível ler a imagem")

    # 3. Decodificação com pyzbar
    decoded_objs = decode(image)
    if not decoded_objs:
        return JSONResponse(status_code=422, content={"detail": "QR Code não encontrado"})

    # 4. Extração de texto
    texts = [obj.data.decode("utf-8") for obj in decoded_objs]
    return {"qrcodes": texts}
