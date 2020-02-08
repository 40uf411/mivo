import socket
import sys
import json

import os, random, string

import json


from pocketsphinx.pocketsphinx import *
from sphinxbase.sphinxbase import *


s = socket.socket()
s.bind(("",9000))
s.listen(10) # Acepta hasta 10 conexiones entrantes.

sc, address = s.accept()

print(address)
i=1
file_name = 'file_'+ str(i)+".wav"

f = open("/var/www/html/mivo/public/uploaded_audios/" + file_name,'wb') # Open in binary
print f
i=i+1
# while (True):
# Recibimos y escribimos en el fichero
l = sc.recv(1024)
b=0
while (l and b<100):
    f.write(l)
    l = sc.recv(1024)
    b=b+1
    print(b)
f.close()
print "done!"
# ________________write your code below_____________________________________

MODEL_DIR = "/var/www/html/mivo/py/model-3/"
DATA_DIR = "/var/www/html/mivo/public/uploaded_audios/"

def reco(data):
    # Create a decoder with certain model
    config = Decoder.default_config()
    config.set_string('-hmm', data["hmm"])
    config.set_string('-lm', data["lm"])
    config.set_string('-dict', data["dict"])
    config.set_string('-logfn', '/dev/null')
    decoder = Decoder(config)

    # Decode streaming data.
    decoder = Decoder(config)
    decoder.start_utt()
    stream = open(data["file"], 'rb')
    while True:
        buf = stream.read(1024)
        if buf:
            decoder.process_raw(buf, False, False)
        else:
            break
    decoder.end_utt()
    return ' '.join([seg.word for seg in decoder.seg()])
    #print [seg.word for seg in decoder.seg()]


d = reco({
        "hmm": MODEL_DIR + "hmm",
        "lm": MODEL_DIR + "m3.lm.DMP",
        "dict": MODEL_DIR + "m3.dic",
        "file": DATA_DIR + file_name
        })

print d

# _____________________________________________________

# sc.send("good".encode('UTF-8'))
sc.send(d.encode('UTF-8') + b"\n")
data = sc.recv(1024)
print ("1", data)
result=1

sc.close()
s.close()

# ss = socket.socket()
# ss.connect(("127.0.0.1",9222))
# HOST = '192.168.43.211'  # The server's hostname or IP address
# HOST='localhost'
# PORT = 65432        # The port used by the server
#
# with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as ss:
#     ss.connect((HOST, PORT))
#     print("here")
# # while True:
#
#     ss.send(str.encode());
# # ss.recv(1024).decode()
#
# ss.close()
# ___________________________________________________________________________

