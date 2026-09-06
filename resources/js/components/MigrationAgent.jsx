import React, { useState } from "react";
export default function MigrationAgent() {
    const [message, setMessage] = useState("");
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState("");

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
            setReply("Something went wrong.");
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="flex h-full min-h-100  flex-col border border-white rounded-xl shadow-lg   ">
           
                <div className="border-b font-semibold text-2xl p-2.5">
                    <h1 className="text-green-400">Migration AI Agent!</h1>
                </div>
                <div className="flex flex-1">
                    <textarea
                        name=""
                        id=""
                        value={data??""}
                        className="w-full resize-none flex-1 flex p-2.5 outline-none"
                    ></textarea>
                </div>
            
            <form onSubmit={handleSubmit} className="p-2.5">
                <div>
                    <input
                        type="text"
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        placeholder="Ask something..."
                        className="w-full p-2.5 border-gray-300 border rounded-2xl mb-5"

                    />
                    <div className="flex gap-5">
                        <button disabled={loading} type="submit">
                            {loading ? "thinking..." : "Send"}
                        </button>
                        <button type="button" onClick={()=>setMessage("")}>Clear</button>
                    </div>
                </div>
            </form>
        </div>
    );
}
