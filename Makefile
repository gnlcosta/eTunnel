APP_DIR=etunnel
PKG_NAME=app_etunnel

all: pkg

pkg: clean
	rm -rf $(APP_DIR)
	mkdir -p $(APP_DIR)
	cp -a www app.json .htaccess start.sh stop.sh etunnel_mng.py etunnel.sh $(APP_DIR)
	tar czvf $(PKG_NAME).tgz $(APP_DIR)
	rm -rf $(APP_DIR)

clean:
	rm -fr www/data/*
	rm -f $(PKG_NAME).tgz*
