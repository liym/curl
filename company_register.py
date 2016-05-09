#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Created by 'lijian' on 5/7/16

"""
ssh -N -D 127.0.0.1:8080 scrapy@120.55.118.42

Request URL:http://vote.ecloud-zj.com/enterprises/Login
Request Method:GET
Status Code:200 OK
Remote Address:120.55.91.150:80

UserName:695722324
Password:12345678
__RequestVerificationToken:CfDJ8GtrgEe0yUNPgLvJx0u6e_5oa1VaQQlrIQkpGHA-Dq672kcW7nMuo9jxkzM6ivyG5c1nV0uBnO-dScH5y1EUGYyXBXWUsgKu5h0zg45iAPsMktajv9KgtG9XZOStGugCHSHF-Fn4z_4bcPCbA_GB
"""
import random
import urllib
import urllib2
import hashlib
import logtool

import requests

logger = logtool.get_file_logger('register.log')

def pwd_code(text):
    m2 = hashlib.md5()
    m2.update(text)
    return m2.hexdigest()[:8]


class Account(object):

    def __init__(self, name, code, contact=None, email=None, phone=None, aid=None, bid=None):
        self.name = name
        self.code = code
        self.email = emails[random.randint(0, len(emails) - 1)][:-1]
        self.contact = contacts[random.randint(0, len(contacts) - 1)]
        self.phone = random_phone()
        self.aid, self.bid = random_id()
        self.pwd = pwd_code(code)

    def __str__(self):
        return 'Account,%s,%s,%s,%s,%s,%s,%s,%s' % (self.code, self.pwd, self.name, self.email, self.contact, self.phone, self.aid, self.bid)


import threading
class CompanyRegiter(threading.Thread):

    def __init__(self, thread_name, accounts):
        threading.Thread.__init__(self)

        self.timeout = 10
        self.accounts = accounts
        self.success_accounts = []
        self.thread_name = thread_name
        self.logger = logtool.get_file_logger(thread_name)

    def run(self):
        for account in self.accounts:
            try:
               session = requests.Session()
               session.proxies = {'http': 'http://127.0.0.1:8118'}
               self.do_register(account, session)
            except Exception as e:
                print e
                pass

    def do_register(self, account, session):
        #定义一个要提交的数据数组(字典)

        url = 'http://www.ecloud-zj.com:88/enterprises/Register'
        r = session.get(url, timeout=self.timeout)
        # print req.read()
        self.logger.debug(self.thread_name + ' prepare to register')

        data = {}
        data['name'] = account.name
        data['codeCertificate'] = account.code
        data['address'] = ''
        data['enterpriseCustomCode'] = ''
        data['email'] = account.email
        data['generalManager'] = account.contact
        data['managerTel'] = account.phone

        data['ParentAID'] = account.aid
        data['ParentBID'] = account.bid

        data['Password'] = account.pwd
        data['ConfirmPassword'] = account.pwd

        #定义post的地址
        url = 'http://www.ecloud-zj.com:88/enterprises/Register'
        post_data = urllib.urlencode(data)

        r = session.post(url, data=post_data, headers={}, timeout=self.timeout)
        #获取提交后返回的信息'<script type="text/javascript"> alert(\\'组织机构代码已经存在\\');javascript:history.go(-1);</script>'
        html = r.text.encode('utf8')

        if html.find("组织机构代码已经存在") > 0:
            self.logger.error("%s regist fail, duplicat code, %s" % (self.thread_name, account))
        else:
            self.success_accounts.append(account)
            self.logger.debug('%s regist success %s' % (self.thread_name, account))
        print account, html


def random_phone():
    return ''.join([
        str(random.choice([138, 182, 133, 180, 188, 135, 155, 152])),
        ''.join(random.sample("1234567890", 8))]
    )


def load_names():
    result = []
    for line in open("conf/names"):
        line_names = line.split("#")
        result.extend(line_names)

    return result


def load_email():
    return open("conf/emails").readlines()


Cates = {
    91 : ['92', '129', '175', '195', '212'],
    1 :['2', '14', '22', '43', '54', '68', '78', '85'],
    221 : ['222', '228', '240', '248', '257', '270'],
    279 : ['280', '285', '289', '294', '299', '307', '320', '328'],
    339 : ['340', '358', '370', '385', '398', '403', '410', '416', '422'],
    451 : ['452', '464', '475', '492', '501', '506', '514', '518', '526'],
    533 : [534],
    538 : ['539', '554', '564', '586', '593'],
    607 : ['608', '617', '627', '633', '638', '645', '660', '670'],
    678 : ['679', '685', '705', '725', '733', '763', '768', '782', '784'],
    797 : ['798', '807', '814', '830', '837', '847'],
    855 : ['856'],
    866 : ['867', '873', '878', '886', '892', '906'],
    914 : ['915', '921', '930', '952', '962', '970', '978', '993'],
    1010 : ['1011', '1024', '1038', '1046', '1056', '1063'],
    1078 : ['1079', '1090', '1100', '1116', '1125', '1132', '1138', '1143'],
    }


def random_id():
    keys = Cates.keys()
    aid = keys[random.randint(0, len(keys)-1)]
    values = Cates[aid]
    bid = values[random.randint(0, len(values)-1)]

    return aid, bid

contacts = load_names()
emails = load_email()

def load_account(account_fname, skip=0):
    result = []
    i = 0
    for line in open(account_fname, "r").readlines():
        if not line or i < skip: # or i < 4403:
            i += 1
            continue

        fs = line.split(',')
        name = fs[0]
        code = fs[1] if len(fs[1]) > 4 else fs[2]
        code = code[:-1] if code.endswith('\n') else code

        account = Account(name, code)
        result.append(account)

    return result


def slice_step(total, pieces):
    return total / pieces + (1 if total % pieces > 0 else 1)


def main(thread_num, company_fname, accounts_file):
    accounts = load_account(company_fname, 0)

    threads = []
    step = slice_step(len(accounts), thread_num)

    for i in xrange(0, thread_num):
        start = i * step
        end = (i + 1) * step
        t = CompanyRegiter('regist_thread_%s' % i, accounts[start:end])
        threads.append(t)

    for t in threads:
        t.start()

    for t in threads:
        t.join()

    out = open(accounts_file, 'w')
    for t in threads:
        for item in t.success_accounts:
            out.write('%s\n' % item)
    out.flush()
    out.close()

if __name__ == '__main__':
    main(1, "conf/aa", 'account1')
