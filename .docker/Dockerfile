# CVE-2026-15176 / CVE-2026-97205 / CVE-2026-95101 / CVE-2026-97629, nginx 1.31.7+ fixed
FROM nginx:1.31.8

ENV TZ=Asia/Taipei
# CVE-2026-109618 / CVE-2026-109619, session.upload_progress UAF RCE
ENV PHP_FPM_PIN=8.4.27*
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && \
	apt-get install -y ca-certificates curl lsb-release && \
	curl -sSL -o /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg && \
	echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
	#add-apt-repository -y ppa:ondrej/php && \
	apt-get update && \
	apt-get install -y php8.4-fpm=$PHP_FPM_PIN php8.4-mysql php8.4-gd php8.4-mbstring && \
	apt-get purge -y --auto-remove && \
	echo "session.upload_progress.enabled = 0" > /etc/php/8.4/fpm/conf.d/99-security.ini

