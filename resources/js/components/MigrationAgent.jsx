import React, {useState} from "react";
export default function MigrationAgent(){
    const [message,setMessage]= useState("");

    return(
        <div className="flex h-50 flex-col border border-white rounded-xl shadow-lg p-5  ">
            <div className="border-b font-semibold text-2xl pb-5  ">
                <h1 className="text-green-400">Migration AI Agent!</h1>
            </div>
            <div>
                <input type="text" placeholder="Ask something..." />
                <button>Send</button>
            </div>
        </div>
    )
}