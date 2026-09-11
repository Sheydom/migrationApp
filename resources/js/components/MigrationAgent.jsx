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
            setReply("Something went wrong.");
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="flex h-full min-h-100  flex-col rounded-xl shadow-lg ">
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
