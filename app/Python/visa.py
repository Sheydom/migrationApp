from pdf2image import convert_from_path
from paddleocr import PaddleOCR
import json
from ollama import chat
import time
from pathlib import Path
import sys
from ollama import Client



visa_path = Path(sys.argv[1])

total = time.perf_counter()
start = time.perf_counter()
pages = convert_from_path(
        visa_path,
    dpi=300
)

# print(f"PDF conversion: {time.perf_counter() - start:.2f}s")

start = time.perf_counter()
image_path = visa_path.with_suffix(".png")
pages[0].save(str(image_path), "PNG")
# print(f"Save PNG: {time.perf_counter() - start:.2f}s")


start = time.perf_counter()
ocr = PaddleOCR(
    lang="en",
    #smaller yolo models-mobile
     text_detection_model_name="PP-OCRv5_mobile_det",
     text_recognition_model_name="PP-OCRv5_mobile_rec",
    use_doc_orientation_classify=True,
    use_doc_unwarping=False,
    use_textline_orientation=True,
)

results = ocr.predict(str(image_path))
# print(f"PaddleOCR: {time.perf_counter() - start:.2f}s")


#  saving processed images on output folder no indentation! except forloop
# base = Path(__file__).resolve().parent
# for result in results:
#     result.save_to_img(f"{base}/output")


# for result in results:
#     result.save_to_img("/Users/dominicknabe/Documents/vscode/migrationApp/app/Python/output")


start = time.perf_counter()
texts = []
for result in results:
     texts.extend(result["rec_texts"])

ocr_text = "\n".join(texts)
if image_path.exists():
    image_path.unlink()

# print(f"Build text: {time.perf_counter() - start:.4f}s")

#display ocr_text for debugging
# print(ocr_text)
start = time.perf_counter()
# comment out client and chance to only chat(nodel ecetera) below for local
client = Client(host="http://ollama:11434")
response = client.chat(

    model="qwen2.5:1.5b",

    messages=[

        {

            "role": "user",

            "content": f"""

Extract visa name.

Rules:

- Never invent information.

- Never guess.

- If a value cannot be found, return null.

- Return and respond ONLY valid JSON.

- No explanation.

Fields:

- current_visa

OCR TEXT:

{ocr_text}

""",

        }

    ],format = "json",
      options={

         "num_ctx": 4096,

        "num_predict": 300,

        "temperature": 0

    }

)


if image_path.exists():
    image_path.unlink()

response_type = type(response)
#print(response)
# print(response_type)
content = response.message.content
content_data = json.loads(content)
print(json.dumps(content_data))

#  important json file for debugging
# base_dir = Path(__file__).resolve().parent
# output_file = base_dir / "passport_data.json"
#
# with open(output_file, "w") as f:
#      json.dump(content_data, f, indent=2)

# print(f"json data successfull created '{response_type}'")
# print(f"Qwen: {time.perf_counter() - start:.2f}s")
# # print("FULL RESPONSE:")
# # print(response)
# print(response.message.content)
# print(f"TOTAL: {time.perf_counter() - total:.2f}s")
