
#
# Makefile fuer wp-forecast
#

po:
	@xgettext -L PHP -k --keyword=_e  --keyword=__ --from-code=utf-8 --default-domain=wp-forecast --output=wp-forecast.pot *.php;
	@for i in lang/*.po; do \
		echo -n "Merging $$i...";\
		msgmerge -q -U $$i wp-forecast.pot;\
		echo "done:";\
	done;\
	echo "Please edit the po files now and start 'make mo'";
mo:
	@for i in lang/*.po;do \
		echo -n "Converting $$i...";\
		msgfmt -o `echo $$i|cut -d"." -f1`.mo $$i; \
		echo "done.";\
	done;