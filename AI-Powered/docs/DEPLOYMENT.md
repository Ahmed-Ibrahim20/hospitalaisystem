# دليل النشر (Deployment Guide)

## 🚀 نشر النظام في بيئة الإنتاج

### 1. متطلبات الخادم

**الحد الأدنى:**
- CPU: 2 cores
- RAM: 4 GB
- Storage: 10 GB
- OS: Ubuntu 20.04+ / Windows Server 2019+
- Python: 3.8+

**الموصى به:**
- CPU: 4+ cores
- RAM: 8+ GB
- Storage: 20+ GB

### 2. التثبيت على Ubuntu/Linux

```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت Python و pip
sudo apt install python3.10 python3-pip python3-venv -y

# إنشاء مستخدم للتطبيق
sudo useradd -m -s /bin/bash diabetes-api
sudo su - diabetes-api

# استنساخ المشروع
git clone <repository-url> /home/diabetes-api/app
cd /home/diabetes-api/app

# إنشاء بيئة افتراضية
python3 -m venv venv
source venv/bin/activate

# تثبيت المتطلبات
pip install -r requirements.txt

# تدريب النموذج
cd models
python baseline_diabetes.py
cd ..
```

### 3. استخدام Systemd (Linux)

إنشاء ملف `/etc/systemd/system/diabetes-api.service`:

```ini
[Unit]
Description=Diabetes Prediction API
After=network.target

[Service]
Type=simple
User=diabetes-api
WorkingDirectory=/home/diabetes-api/app/deployment
Environment="PATH=/home/diabetes-api/app/venv/bin"
ExecStart=/home/diabetes-api/app/venv/bin/uvicorn fastapi_service:app --host 0.0.0.0 --port 8000 --workers 4
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

تفعيل الخدمة:
```bash
sudo systemctl daemon-reload
sudo systemctl enable diabetes-api
sudo systemctl start diabetes-api
sudo systemctl status diabetes-api
```

### 4. استخدام Nginx كـ Reverse Proxy

تثبيت Nginx:
```bash
sudo apt install nginx -y
```

إنشاء ملف `/etc/nginx/sites-available/diabetes-api`:

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

تفعيل:
```bash
sudo ln -s /etc/nginx/sites-available/diabetes-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. تفعيل HTTPS مع Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com
```

### 6. استخدام Docker

إنشاء `Dockerfile`:

```dockerfile
FROM python:3.10-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 8000

CMD ["uvicorn", "deployment.fastapi_service:app", "--host", "0.0.0.0", "--port", "8000"]
```

إنشاء `docker-compose.yml`:

```yaml
version: '3.8'

services:
  api:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - ./models/saved:/app/models/saved
    environment:
      - DIABETES_API_TOKEN=${DIABETES_API_TOKEN}
    restart: unless-stopped
```

تشغيل:
```bash
docker-compose up -d
```

### 7. المراقبة والصيانة

**Logs:**
```bash
# Systemd logs
sudo journalctl -u diabetes-api -f

# Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
```

**Monitoring:**
- استخدم Prometheus + Grafana
- راقب CPU, Memory, Response Time
- راقب معدل الطلبات والأخطاء

### 8. النسخ الاحتياطي

```bash
# نسخ احتياطي للنموذج
tar -czf diabetes-model-backup-$(date +%Y%m%d).tar.gz models/saved/

# نسخ احتياطي للبيانات
tar -czf data-backup-$(date +%Y%m%d).tar.gz data/
```

### 9. التحديثات

```bash
cd /home/diabetes-api/app
git pull
source venv/bin/activate
pip install -r requirements.txt --upgrade
sudo systemctl restart diabetes-api
```

### 10. الأمان

- ✅ استخدم HTTPS فقط
- ✅ فعّل JWT authentication
- ✅ استخدم firewall (ufw)
- ✅ حدّث النظام بانتظام
- ✅ استخدم strong passwords
- ✅ راقب السجلات

```bash
# تفعيل firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

**للدعم:** افتح Issue في المشروع
