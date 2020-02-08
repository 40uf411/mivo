import os, random, string

import json

from os import environ, path
from flask import *

from pocketsphinx.pocketsphinx import *
from sphinxbase.sphinxbase import *

MODELDIR = "/var/www/html/mivo/py/model-3/"
DATADIR = "/var/www/html/mivo/public/uploaded_audios/"

app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def index():
    return"""<html><head></head><body><form action="/data" method="POST">
            <input type="text" name="name" id ="name">
            <input type="submit">
            </form></body></html>"""
    
@app.route('/reco', methods=['POST'])
def data():
    if request.method != 'POST':
        Response(status= 200,
                    mimetype= "application/json",
                    response="error")
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
        #return ' '.join([seg.word for seg in decoder.seg()])
        return json.dumps([seg.word for seg in decoder.seg()])
    print DATADIR + request.form["file"]
    resp = reco({"hmm": MODELDIR + 'hmm',
                "lm": MODELDIR + 'm3.lm.DMP',
                "dict": MODELDIR + 'm3.dic',
                "file": DATADIR + request.form["file"]
                })
    print resp
    return Response(status= 200,
                    mimetype= "application/json",
                    response=resp)

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
        #return ' '.join([seg.word for seg in decoder.seg()])
        return json.dumps([seg.word for seg in decoder.seg()])
    

if __name__ == '__main__':
    app.run(debug=True)



