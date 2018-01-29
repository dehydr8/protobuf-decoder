protobuf-decoder
================
[![Build Status](https://travis-ci.org/dehydr8/protobuf-decoder.svg?branch=master)](https://travis-ci.org/dehydr8/protobuf-decoder)

*protobuf-decoder* is a PHP library that reads raw protobuf buffers and returns a sane representation of the data.

The protobuf encoding format can be found [here](https://developers.google.com/protocol-buffers/docs/encoding).

## Notes
1. Wiretypes 3 `(start group)`, 4 `(end group)` and 5 `(32bit fixed)` not implemented as of yet

## Representation

```
00000000  08 8f 81 eb cf e0 2a 12  08 6b 6f 74 6c 69 6e 34  |......*..kotlin4|
00000010  36 3a 05 00 01 03 04 07  42 00 48 fa 01 55 00 00  |6:......B.H..U..|
00000020  48 43 72 0a 0a 08 50 4f  4b 45 43 4f 49 4e 72 0c  |HCr...POKECOINr.|
00000030  0a 08 53 54 41 52 44 55  53 54 10 64              |..STARDUST.d|
0000003c
```
to
```json
[
    {
        "field": "1",
        "value": "1469046243471"
    },
    {
        "field": "2",
        "value": "kotlin46"
    },
    {
        "field": "7",
        "value": "\u0000\u0001\u0003\u0004\u0007"
    },
    {
        "field": "8",
        "value": ""
    },
    {
        "field": "9",
        "value": "250"
    },
    {
        "field": "10",
        "value": "00004843"
    },
    {
        "field": "14",
        "value": [
            {
                "field": "1",
                "value": "POKECOIN"
            }
        ]
    },
    {
        "field": "14",
        "value": [
            {
                "field": "1",
                "value": "STARDUST"
            },
            {
                "field": "2",
                "value": "100"
            }
        ]
    }
]
```