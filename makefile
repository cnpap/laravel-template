# 小单元 unit
testDev:
	vendor/bin/phpunit --testsuite Dev

testAdmin:
	vendor/bin/phpunit --testsuite Admin

testAuth:
	vendor/bin/phpunit --testsuite Auth

# 大单元
testUnit:
	vendor/bin/phpunit --testsuite Unit

testClear:
	rm storage/logs/dev*.log

test:
	make testUnit;
