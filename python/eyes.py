#!/usr/bin/env python
# Software License Agreement (BSD License)
#
# Copyright (c) 2012, Philipp Wagner
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions
# are met:
#
#  * Redistributions of source code must retain the above copyright
#    notice, this list of conditions and the following disclaimer.
#  * Redistributions in binary form must reproduce the above
#    copyright notice, this list of conditions and the following
#    disclaimer in the documentation and/or other materials provided
#    with the distribution.
#  * Neither the name of the author nor the names of its
#    contributors may be used to endorse or promote products derived
#    from this software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
# "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
# LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
# FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
# COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
# INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
# BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
# CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
# LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
# ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

import os
import sys
import cv2
#import cv
import cv2.cv as cv
import numpy as np
import math, Image

GlobalSizeModifier = 1

storage = cv.CreateMemStorage()
imcolor = cv.LoadImage('train/s1/528px-Arnold_Schwarzenegger_edit(ws).jpg') # input image
# loading the classifiers
haarFace = cv.Load('data/haarcascade_frontalface_alt2.xml', storage)
# haarEyes = cv.Load('data/haarcascade_eye.xml')
haarEyes = cv.Load('data/haarcascade_mcs_eyepair_big.xml', storage)
haarNose = cv.Load('data/haarcascade_mcs_nose.xml', storage)
haarMouth = cv.Load('data/haarcascade_mcs_mouth.xml', storage)


def detectEyes(img, r):
    cv.SetImageROI(img, (
      r[0],
      int(r[1] + (r[3] / 5.5)),
      r[2],
      int(r[3] / 3.0)
    ))

    detectedEyes = cv.HaarDetectObjects(img, haarEyes, storage, 1.15, 3, 0, (25 * GlobalSizeModifier, 15 * GlobalSizeModifier))

    # draw a purple rectangle where the eye is detected
    if detectedEyes:
        for face in detectedEyes:
            cv.Rectangle(img,(face[0][0],face[0][1]),
                   (face[0][0]+face[0][2],face[0][1]+face[0][3]),
                   cv.RGB(155, 55, 200),2)

def detectNose(img, r):
    cv.SetImageROI(img, (
      int(r[0]),
      int(r[1]),
      int(r[2]),
      int(r[3])
    ))

    detectedNose = cv.HaarDetectObjects(img, haarNose, storage, 1.15, 3, 0, (25 * GlobalSizeModifier, 15 * GlobalSizeModifier))

    # draw a purple yellow where the nose is detected
    if detectedNose:
        for face in detectedNose:
            cv.Rectangle(img,(face[0][0],face[0][1]),
                   (face[0][0]+face[0][2],face[0][1]+face[0][3]),
                   cv.RGB(255, 216, 0),2)

def detectMouth(img, r):
    cv.SetImageROI(img, (
      r[0],
      int(r[1] + (r[3] * 2 / 3)),
      r[2],
      int(r[3] / 3)
    ))

    detectedMouth = cv.HaarDetectObjects(img, haarMouth, storage, 1.15, 4, 0, (25 * GlobalSizeModifier, 15 * GlobalSizeModifier))

    # draw a red rectangle where the mouth is detected
    if detectedMouth:
        for face in detectedMouth:
            cv.Rectangle(img,(face[0][0],face[0][1]),
                   (face[0][0]+face[0][2],face[0][1]+face[0][3]),
                   cv.RGB(255, 0, 0),2)

# running the classifiers
detectedFace = cv.HaarDetectObjects(imcolor, haarFace, storage, 1.2, 2, cv.CV_HAAR_DO_CANNY_PRUNING, (20 * GlobalSizeModifier, 20 * GlobalSizeModifier))

# draw a green rectangle where the face is detected
if detectedFace:
    GlobalSizeModifier = int(imcolor.width / 180)

    for face in detectedFace:
        cv.Rectangle(imcolor,(face[0][0],face[0][1]),
               (face[0][0]+face[0][2],face[0][1]+face[0][3]),
               cv.RGB(155, 255, 25),2)

        r = (
          face[0][0],
          face[0][1],
          face[0][2],
          face[0][3]
        )

        detectEyes(imcolor, r)
        cv.ResetImageROI(imcolor)
        detectNose(imcolor, r)
        cv.ResetImageROI(imcolor)
        detectMouth(imcolor, r)
        cv.ResetImageROI(imcolor)

    cv.ResetImageROI(imcolor)

cv.NamedWindow('Face Detection', cv.CV_WINDOW_AUTOSIZE)
cv.ShowImage('Face Detection', imcolor) 
cv.WaitKey()