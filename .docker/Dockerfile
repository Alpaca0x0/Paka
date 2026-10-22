FROM nginx

ENV TZ=Asia/Taipei
# CVE-2026-109618 / CVE-2026-109619, session.upload_progress UAF RCE
ENV PHP_FPM_PIN=8.2.35*
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && \
	apt-get install -y software-properties-common gnupg2 && \
	apt-key adv --fetch-keys https://packages.sury.org/php/apt.gpg && \
	echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
	#add-apt-repository -y ppa:ondrej/php && \
	apt-get update && \
	apt-get install -y php8.2-fpm=$PHP_FPM_PIN php8.2-mysql php8.2-gd php8.2-mbstring && \
	apt-get purge -y --auto-remove && \
	echo "session.upload_progress.enabled = 0" > /etc/php/8.2/fpm/conf.d/99-security.ini

