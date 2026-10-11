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
#for producition
client = Client(host="http://ollama:11434")
# response = client.chat(
response = client.chat(
    model="qwen2.5:1.5b",

    messages=[

        {

            "role": "user",

            "content": f"""

You are an information extraction assistant for Australian immigration documents.

Extract the Australian visa type and subclass from the OCR text.

RULES:

- Extract information ONLY from the provided OCR text.

- Never invent, assume, or guess information.

- Return ONLY valid JSON.

- Do not include explanations or markdown.

- Use exactly the JSON key specified below.

- If no visa type or subclass can be identified, return null.

- Do not confuse a visa application number, grant number, or passport number with a visa subclass.

- Do not extract visa types mentioned only as historical visas or unrelated references.

- Prefer the visa identified as granted or currently held in the document.

VISA FORMATTING:

- If both visa name and subclass are available, return:

  "Visa Name (subclass XXX)"

- If only the subclass is available, return:

  "Subclass XXX"

- If only the visa name is available, return the visa name exactly as written.

- Do not invent a visa name from a subclass number.

- Preserve the official visa name when available.

EXAMPLES:

Input:

Visa: Temporary Skill Shortage

Visa subclass: 482

Output:

{{"current_visa": "Temporary Skill Shortage (subclass 482)"}}

Input:

Visa: Student

Visa subclass: 500

Output:

{{"current_visa": "Student (subclass 500)"}}

Input:

Visa: Skilled Independent

Visa subclass: 189

Output:

{{"current_visa": "Skilled Independent (subclass 189)"}}

Input:

Visa subclass: 600

Output:

{{"current_visa": "Subclass 600"}}

Input:

No visa information available

Output:

{{"current_visa": null}}

Return JSON containing exactly this field:

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
base_dir = Path(__file__).resolve().parent
output_file = base_dir / "passport_data.json"
#
with open(output_file, "w") as f:
     json.dump(content_data, f, indent=2)

# print(f"json data successfull created '{response_type}'")
# print(f"Qwen: {time.perf_counter() - start:.2f}s")
# # print("FULL RESPONSE:")
# # print(response)
# print(response.message.content)
# print(f"TOTAL: {time.perf_counter() - total:.2f}s")
