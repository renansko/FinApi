FROM python:3.10-slim

# 1. Instala dependências do sistema em um único RUN
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      libzbar0 \
      libzbar-dev && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

# 2. Instala pacotes Python
COPY app/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 3. Copia o código e define o comando de inicialização
COPY app/main.py .
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
