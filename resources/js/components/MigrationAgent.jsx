import React, { useState } from "react";
export default function MigrationAgent() {
    const [message, setMessage] = useState("");
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState("");

    const handleClear = () => {
        setData("");
        setMessage("");
    };

     async function handleSubmit(e) {
         e.preventDefault();
         if (!message.trim()) {
             return;
         }
         try {
             setLoading(true);
             const res = await fetch("api/agent/chat", {
                 method: "POST",
                 headers: {
                     "Content-Type": "application/json",
                     Accept: "application/json",
                 },
                 body: JSON.stringify({ message: message }),
             });
             if (!res.ok) {
                 throw new Error("Something went wrong while sending.");
             }
             console.log("success");
             const data = await res.json();
             setData(data.message);
             console.log(data.message);
         } catch (error) {
             console.error(error);
             setData(`Something went wrong sorry. ${error.message}`);
         } finally {
             setLoading(false);
         }
     }
//     async function handleSubmit(e) {
//     e.preventDefault();

//     if (!message.trim()) {
//         return;
//     }

//     try {
//         setLoading(true);
//         setData("");

//         const res = await fetch("api/agent/chat", {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//                 Accept: "application/x-ndjson",
//             },
//             body: JSON.stringify({ message: message }),
//         });

//         if (!res.ok) {
//             throw new Error("Something went wrong while sending.");
//         }

//         if (!res.body) {
//             throw new Error("Streaming response not available.");
//         }

//         const reader = res.body.getReader();
//         const decoder = new TextDecoder();

//         let buffer = "";

//         while (true) {
//             const { value, done } = await reader.read();

//             if (done) {
//                 break;
//             }

//             buffer += decoder.decode(value, { stream: true });

//             const lines = buffer.split("\n");

//             // Keep an incomplete JSON line for the next chunk
//             buffer = lines.pop() ?? "";

//             for (const line of lines) {
//                 if (!line.trim()) {
//                     continue;
//                 }

//                 const chunk = JSON.parse(line);

//                 if (chunk.error) {
//                     throw new Error(chunk.error);
//                 }

//                 const content = chunk.message?.content ?? "";

//                 if (content) {
//                     setData((current) => current + content);
//                 }
//             }
//         }

//     } catch (error) {
//         console.error(error);
//         setData(`Something went wrong sorry. ${error.message}`);
//     } finally {
//         setLoading(false);
//     }
// }

    return (
        <div className="flex h-full min-h-100  flex-col rounded-xl shadow-lg">
            <div className="flex flex-col flex-1">
                <div className=" pb-2.5 font-semibold text-2xl">
                    <h1 className="text-green-400">Migration AI!</h1>
                </div>
                <textarea
                    readOnly
                    name=""
                    id=""
                    value={data ?? ""}
                    className="w-full resize-none flex-1 flex p-2.5 outline-none bg-black rounded-2xl mb-5"
                ></textarea>
            </div>

            <form onSubmit={handleSubmit} className="flex flex-col">
                <div>
                    <input
                        type="text"
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        placeholder="Ask something..."
                        className={`w-full p-2.5 border-gray-300/30 border rounded-2xl mb-5 hover:border-[#51A2FF] duration-300 ease-in-out focus:border focus:outline-none focus:border-[#51A2FF] `}
                    />
                    <div className="flex gap-5">
                        <button
                            disabled={loading}
                            type="submit"
                            className="border-[#FE9900]/30 hover:border-[#FE9900] hover:scale-110 hover:text-black hover:bg-[#FE9900] border rounded-2xl p-2.5 duration-300 ease-in-out"
                        >
                            {loading ? "thinking..." : "Send"}
                        </button>
                        <button
                            type="button"
                            onClick={handleClear}
                            className="border-[#FE9900]/30 hover:border-[#FE9900] hover:scale-110 hover:text-black hover:bg-[#FE9900] border rounded-2xl p-2.5 duration-300 ease-in-out"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
}
