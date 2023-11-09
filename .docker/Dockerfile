FROM nginx

ENV TZ=Asia/Taipei
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && \
	apt-get install -y software-properties-common gnupg2 && \
	apt-key adv --fetch-keys https://packages.sury.org/php/apt.gpg && \
	echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
	#add-apt-repository -y ppa:ondrej/php && \
	apt-get update && \
	apt-get install -y php8.2-fpm php8.2-mysql php8.2-gd php8.2-mbstring && \
	apt-get purge -y --auto-remove

