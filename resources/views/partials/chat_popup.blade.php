{{-- LAB 7: CHAT POPUP COMPONENT (PDF Hướng dẫn Lab 7) --}}
@auth
<div id="chat-box">
    <button id="chat-toggle" class="btn btn-primary rounded-circle shadow" type="button" aria-label="Tư vấn cùng Ha Thu">@include('partials.icon', ['name' => 'chat', 'size' => 22])</button>
    <div id="chat-popup" class="card shadow-lg" style="display:none; border-radius: 12px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #db2777, #be185d) !important; border:none; padding:12px 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 16px;">🌸</span>
                <strong style="font-size: 14px; letter-spacing: 0.3px;">Hỗ trợ khách hàng</strong>
            </div>
            <button id="chat-close" class="btn btn-sm btn-light" type="button" style="padding: 2px 8px; font-weight: bold; border-radius: 6px; font-size: 12px; line-height: 1;">✕</button>
        </div>
        <div id="chat-messages" class="card-body">
            <div class="text-center text-muted"><small>Đang tải lịch sử...</small></div>
        </div>
        <div class="card-footer bg-white" style="border-top: 1px solid #f3f4f6; padding: 10px 14px;">
            <div class="input-group" style="display: flex; gap: 6px;">
                <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off" style="border-radius: 8px; border: 1px solid #e5e7eb; font-size: 13.5px; padding: 8px 12px; outline: none; flex: 1;">
                <div class="input-group-append">
                    <button id="send-btn" class="btn btn-success" type="button" style="background: #db2777; border-color: #db2777; border-radius: 8px; font-weight: 600; padding: 8px 16px; font-size: 13.5px;">Gửi</button>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div id="chat-box">
    <a href="{{ route('login') }}" id="chat-toggle" class="btn btn-primary rounded-circle shadow" title="Đăng nhập để chat với bộ phận hỗ trợ" style="text-decoration: none;">💬 Chat</a>
</div>
@endauth

<style>
/* ── LAB 7: CHAT BOX POPUP STYLES ── */
#chat-box {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999999;
}
#chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #db2777, #be185d) !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    box-shadow: 0 6px 20px rgba(219, 39, 119, 0.45) !important;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s !important;
}
#chat-toggle:hover {
    transform: translateY(-3px) scale(1.05) !important;
    box-shadow: 0 10px 25px rgba(219, 39, 119, 0.55) !important;
}
#chat-popup {
    width: 340px;
    height: 450px;
    background: #ffffff;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.16);
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #fce7f3;
}
#chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #faf8f9;
}
.message-row {
    padding: 8px 13px;
    border-radius: 12px;
    font-size: 0.88rem;
    line-height: 1.4;
    max-width: 82%;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.user-msg {
    align-self: flex-end;
    background: #fce7f3;
    color: #9d174d;
    border-bottom-right-radius: 3px;
    text-align: right;
}
.admin-msg {
    align-self: flex-start;
    background: #ffffff;
    color: #1f2937;
    border-bottom-left-radius: 3px;
    border: 1px solid #f3f4f6;
    text-align: left;
}
</style>

@auth
<script>
(function () {
    function initUserChat() {
        const toggleBtn = document.getElementById("chat-toggle");
        const chatPopup = document.getElementById("chat-popup");
        const closeBtn = document.getElementById("chat-close");
        const sendBtn = document.getElementById("send-btn");
        const input = document.getElementById("chat-input");
        const chatBox = document.getElementById("chat-messages");

        if (!toggleBtn || !chatPopup) return;

        // --- MỞ / ĐÓNG CHAT ---
        toggleBtn.onclick = () => {
            chatPopup.style.display = "flex";
            toggleBtn.style.display = "none";
            loadMessages();
            if (input) setTimeout(() => input.focus(), 150);
        };

        if (closeBtn) {
            closeBtn.onclick = () => {
                chatPopup.style.display = "none";
                toggleBtn.style.display = "flex";
            };
        }

        // --- LOAD TIN NHẮN ---
        function loadMessages() {
            fetch("{{ route('user.chat.messages') }}")
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (!messages || messages.length === 0) {
                        html = "<div class='text-center text-muted' style='margin:auto; color:#9ca3af;'><small>🌸 Bắt đầu cuộc trò chuyện với tư vấn viên Ha Thu Perfume</small></div>";
                    } else {
                        messages.forEach(msg => {
                            const isMe = msg.sender_id == "{{ Auth::id() }}";
                            html += `
                                <div class="message-row ${isMe ? 'user-msg' : 'admin-msg'}">
                                    <strong>${isMe ? 'Bạn' : 'Admin'}:</strong> ${escapeHtml(msg.content)}
                                </div>
                            `;
                        });
                    }
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // --- GỬI TIN NHẮN ---
        function sendMessage() {
            let message = input ? input.value.trim() : "";
            if (message === "") return;

            if (input) input.disabled = true;
            if (sendBtn) sendBtn.disabled = true;

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const token = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

            fetch("{{ route('user.chat.send') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                if (input) {
                    input.value = "";
                    input.disabled = false;
                    input.focus();
                }
                if (sendBtn) sendBtn.disabled = false;
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin:", err);
                if (input) input.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
            });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        if (sendBtn) sendBtn.onclick = sendMessage;
        if (input) {
            input.addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    sendMessage();
                }
            });
        }

        // --- AUTO REFRESH (3 giây/lần) ---
        setInterval(() => {
            if (chatPopup && chatPopup.style.display !== "none") {
                loadMessages();
            }
        }, 3000);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initUserChat);
    } else {
        initUserChat();
    }
})();
</script>
@endauth
