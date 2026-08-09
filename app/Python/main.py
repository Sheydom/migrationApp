from pdf2image import convert_from_path
from paddleocr import PaddleOCR
import json
from ollama import chat
import time
from pathlib import Path
total = time.perf_counter()
start = time.perf_counter()
pages = convert_from_path(
        "/Users/dominicknabe/Documents/vscode/migrationApp/app/Python/sheylaPassport.pdf",
    dpi=300,poppler_path="/opt/homebrew/bin"
)

# print(f"PDF conversion: {time.perf_counter() - start:.2f}s")

start = time.perf_counter()
pages[0].save("passport.png", "PNG")
# print(f"Save PNG: {time.perf_counter() - start:.2f}s")


start = time.perf_counter()
ocr = PaddleOCR(
    lang="en",
    #  text_detection_model_name="PP-OCRv5_mobile_det",
    #  text_recognition_model_name="PP-OCRv5_mobile_rec",
    use_doc_orientation_classify=True,
    use_doc_unwarping=False,
    use_textline_orientation=True,
)

results = ocr.predict("passport.png")
# print(f"PaddleOCR: {time.perf_counter() - start:.2f}s")

    # shows detected data
    # for result in results:
    #     result.print()
    # result.save_to_img("./output")
    # result.save_to_json("./output")
start = time.perf_counter()
texts = []
for result in results:
     texts.extend(result["rec_texts"])

ocr_text = "\n".join(texts)

# print(f"Build text: {time.perf_counter() - start:.4f}s")

#display ocr_text for debugging
# print(ocr_text)
start = time.perf_counter()
response = chat(

    model="qwen2.5:1.5b",

    messages=[

        {

            "role": "user",

            "content": f"""

Extract passport information ONLY from the OCR text below.

Rules:

- Never invent information.

- Never guess.

- If a value cannot be found, return null.

- Return and respond ONLY valid JSON.

- No explanation.

Fields:

- passport_number

- surname

- given_names

- nationality

- date_of_birth

- sex

- place_of_birth

- issue_date

- expiry_date

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




response_type = type(response)
# print(response)
# print(response_type)
content = response.message.content
content_data = json.loads(content)
print(json.dumps(content_data))
# base_dir = Path(__file__).resolve().parent
#
# output_file = base_dir / "passport_data.json"
#
# with open(output_file, "w") as f:
#
#     json.dump(content_data, f, indent=2)

# print(f"json data successfull created '{response_type}'")
# print(f"Qwen: {time.perf_counter() - start:.2f}s")
# # print("FULL RESPONSE:")
# # print(response)
# print(response.message.content)
# print(f"TOTAL: {time.perf_counter() - total:.2f}s")
