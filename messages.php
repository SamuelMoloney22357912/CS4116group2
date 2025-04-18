<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php 
        session_start(); 
        if (!isset($_SESSION['user_id'])) {
            echo "<script>window.location.href='login.php';</script>";
            exit;
        }
    ?>
<script>
const CURRENT_USER_ID = <?php echo json_encode($_SESSION['user_id']); ?>;
</script>
</head>
<body class="h-screen flex flex-col">
    
    <!-- Top Bar -->
<div class="bg-[#67AB9F] text-white text-xl font-bold p-4 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <a href="index.php" class="text-white text-lg hover:underline">&larr; Back</a>
        <span>Messages</span>
    </div>
    <button onclick="openPopup()" class="text-white text-2xl">+</button>
</div>

<!-- Main Content -->
<div class="flex flex-grow overflow-hidden">

    <!-- Sidebar -->
    <div class="w-1/4 bg-[#F0EFEF] border-r border-[#67AB9F] overflow-y-auto p-4">
        <div class="mb-4">
            <div class="font-semibold cursor-pointer text-[#67AB9F] mb-2" onclick="toggleUserDropdown()">+ Start New Conversation</div>
            <div id="userDropdown" class="hidden">
                <select id="userSelect" class="w-full p-2 border border-[#67AB9F] rounded" onchange="startConversation()">
                    <option disabled selected>Select a user...</option>
                </select>
            </div>
        </div>
        <ul id="userList"></ul>
    </div>

    <!-- Chat Section -->
    <div class="flex flex-col flex-grow bg-[#F0EFEF]">
        <div id="chatArea" class="flex-grow p-4 overflow-y-auto"></div>
        <div class="p-4 border-t border-[#67AB9F] flex">
            <input type="text" id="messageInput" class="flex-grow p-2 border border-[#67AB9F] rounded-lg" placeholder="Type a message...">
            <button id="sendBtn" class="ml-2 bg-[#67AB9F] text-white px-4 py-2 rounded-lg">Send</button>
        </div>
    </div>
</div>

<!-- Popup Modal -->
<div id="popup" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
    <div class="bg-[#F0EFEF] p-6 rounded-lg w-96 border border-[#67AB9F]">
        <h2 class="text-lg font-bold mb-4">Select a Business</h2>
        <p class="mb-2">Select a business you would like to talk to a customer from</p>
        <select id="businessSelect" class="w-full p-2 border border-[#67AB9F] rounded mb-4" onchange="updateCustomerList()">
            <option value="Business A">Business A</option>
            <option value="Business B">Business B</option>
            <option value="Business C">Business C</option>
        </select>
        <h2 class="text-lg font-bold mb-2">Select a Customer</h2>
        <select id="customerSelect" class="w-full p-2 border border-[#67AB9F] rounded mb-4"></select>
        <div class="flex justify-end">
            <button onclick="closePopup()" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
            <button class="ml-2 bg-[#67AB9F] text-white px-4 py-2 rounded">Confirm</button>
        </div>
    </div>
</div>

    
    <script>
    let ACTIVE_RECIPIENT_ID = null;

    function openPopup() {
        document.getElementById("popup").classList.remove("hidden");
    }

    function closePopup() {
        document.getElementById("popup").classList.add("hidden");
    }

    // function updateCustomerList() {
    //     const business = document.getElementById("businessSelect").value;
    //     const customerSelect = document.getElementById("customerSelect");
    //     customerSelect.innerHTML = "";

    //     customers[business].forEach(customer => {
    //         let option = document.createElement("option");
    //         option.textContent = customer;
    //         customerSelect.appendChild(option);
    //     });
    // }

    function toggleUserDropdown() {
        const dropdown = document.getElementById("userDropdown");
        dropdown.classList.toggle("hidden");
        fetchAllUsers();
    }

    function fetchAllUsers() {
        fetch("get_all_users.php")
            .then(res => res.json())
            .then(users => {
                const userSelect = document.getElementById("userSelect");
                userSelect.innerHTML = `<option disabled selected>Select a user...</option>`;
                users.forEach(user => {
                    let option = document.createElement("option");
                    option.value = user.user_id;
                    option.textContent = user.user_name;
                    userSelect.appendChild(option);
                });
            });
    }

    function startConversation() {
        const userSelect = document.getElementById("userSelect");
        const selectedUserId = userSelect.value;
        const selectedUserName = userSelect.options[userSelect.selectedIndex].text;

        ACTIVE_RECIPIENT_ID = selectedUserId;
        openChat(selectedUserName);
    }

    function openChat(userName) {
        document.getElementById("chatArea").innerHTML = `<div class='text-gray-500 text-sm mb-2'>Chat with ${userName}</div>`;
        loadMessages();
    }

    function fetchConversations() {
        fetch("get_conversations.php")
            .then(res => res.json())
            .then(users => {
                const userList = document.getElementById("userList");
                userList.innerHTML = "";
                users.forEach(user => {
                    const li = document.createElement("li");
                    li.className = "p-2 border-b cursor-pointer";
                    li.textContent = user.user_name;
                    li.onclick = () => {
                        ACTIVE_RECIPIENT_ID = user.user_id;
                        openChat(user.user_name);
                    };
                    userList.appendChild(li);
                });
            });
    }

    function loadMessages() {
    fetch(`receive_messages.php?user_id=${CURRENT_USER_ID}`)
        .then(res => res.json())
        .then(data => {
            const chatArea = document.getElementById("chatArea");
            chatArea.innerHTML = "";

            data
                .filter(msg =>
                    (msg.sender_id == CURRENT_USER_ID && msg.recipient_id == ACTIVE_RECIPIENT_ID) ||
                    (msg.recipient_id == CURRENT_USER_ID && msg.sender_id == ACTIVE_RECIPIENT_ID)
                )
                .forEach(msg => {
                    const wrapper = document.createElement("div");
                    wrapper.className = (msg.sender_id == CURRENT_USER_ID) 
                        ? "ml-auto max-w-xs mb-2 text-right"
                        : "max-w-xs mb-2 text-left";

                    const bubble = document.createElement("div");
                    bubble.className = (msg.sender_id == CURRENT_USER_ID) 
                        ? "bg-[#67AB9F] text-white p-3 rounded-lg"
                        : "bg-white text-black p-3 rounded-lg";

                    bubble.textContent = msg.content;

                    const timestamp = document.createElement("div");
                    timestamp.className = "text-xs text-gray-500 mt-1";
                    timestamp.textContent = msg.sent_at;

                    wrapper.appendChild(bubble);
                    wrapper.appendChild(timestamp);

                    chatArea.appendChild(wrapper);
                });
        });
    }


    document.getElementById("sendBtn").addEventListener("click", () => { 
    const message = document.getElementById("messageInput").value;

    if (!ACTIVE_RECIPIENT_ID || !message.trim()) return;

    fetch("send_messages.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            sender_id: CURRENT_USER_ID,
            recipient_id: ACTIVE_RECIPIENT_ID,
            message: message.trim()
        })
    })
    .then(res => {
        if (!res.ok) throw new Error("Network response not ok");
        return res.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);
        console.log("Message sent!");
        document.getElementById("messageInput").value = "";
        document.getElementById("messageInput").focus();
        loadMessages();
    })
    .catch(err => {
        console.error("Fetch error:", err.message);
        alert("Error sending message: " + err.message);
    });
});


    // Initial load
    fetchConversations();
    </script>
</body>
</html>
