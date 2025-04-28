<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php 
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/php-error.log');

    session_start(); 
    include("connection.php"); 
    include("functions.php");

    $user_data = check_login($con);

    if (!isset($_SESSION['user_id'])) {
        echo "<script>window.location.href='login.php';</script>";
        exit;
    }
    ?>
    <script>
    const CURRENT_USER_ID = <?php echo json_encode($_SESSION['user_id']); ?>;
    const CURRENT_USER_IS_BUSINESS = <?php echo json_encode($user_data['business'] == 1); ?>;
    </script>
</head>
<body class="h-screen flex flex-col">

<!-- Top Bar -->
<div class="bg-[#67AB9F] text-white text-xl font-bold p-4 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <a href="index.php" class="text-white text-lg hover:underline">&larr; Back</a>
        <span>Inquiries</span>
    </div>
    <button onclick="openPopup()" class="text-white text-2xl">+</button>
</div>

<!-- Main Content -->
<div class="flex flex-grow overflow-hidden">
    <!-- Sidebar -->
    <div class="w-1/4 bg-[#F0EFEF] border-r border-[#67AB9F] overflow-y-auto p-4">
        <div class="mb-4">
            <div class="font-semibold cursor-pointer text-[#67AB9F] mb-2" onclick="toggleBusinessDropdown()">+ Start New Inquiry</div>
            <div id="businessDropdown" class="hidden">
                <select id="sidebarBusinessSelect" class="w-full p-2 border border-[#67AB9F] rounded" onchange="startInquiry()">
                    <option disabled selected>Select a Business...</option>
                </select>
            </div>
        </div>
        <ul id="businessList"></ul>
    </div>

    <!-- Chat Section -->
    <div class="flex flex-col flex-grow bg-[#F0EFEF]">
        <div id="chatArea" class="flex-grow p-4 overflow-y-auto"></div>
        <div class="p-4 border-t border-[#67AB9F] flex">
            <input type="text" id="inquiryInput" class="flex-grow p-2 border border-[#67AB9F] rounded-lg" placeholder="Inquire Here">
            <button id="sendInquiry" class="ml-2 bg-[#67AB9F] text-white px-4 py-2 rounded-lg">Send</button>
        </div>
    </div>
</div>

<!-- Popup Modal -->
<div id="popup" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
    <div class="bg-[#F0EFEF] p-6 rounded-lg w-96 border border-[#67AB9F]">
        <h2 class="text-lg font-bold mb-4">Select a Business</h2>
        <p class="mb-2">Select a business you would like to talk to a customer from</p>
        <select id="modalBusinessSelect" class="w-full p-2 border border-[#67AB9F] rounded mb-4" onchange="updateCustomerList()">
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
window.addEventListener("DOMContentLoaded", function () {
    let ACTIVE_RECIPIENT_ID = null;

    document.getElementById("sendInquiry").addEventListener("click", () => {
        const inquiry = document.getElementById("inquiryInput").value;

        if (!ACTIVE_RECIPIENT_ID || !inquiry.trim()) return;

        const lastInquiry = window.lastLoadedInquiry;
        if (lastInquiry && lastInquiry.status === "Rejected") {
            alert("This inquiry has been rejected. You cannot send more messages.");
            return;
        }

        fetch("send_inquiries.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                sender_id: CURRENT_USER_ID,
                service_id: ACTIVE_SERVICE_ID,
                inquiry: inquiry.trim()
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            document.getElementById("inquiryInput").value = "";
            document.getElementById("inquiryInput").focus();
            loadInquiries();
        })
        .catch(err => alert("Error sending inquiry: " + err.message));
    });

    fetchConversations();

    function fetchConversations() {
        fetch("get_inquiries.php")
            .then(res => res.json())
            .then(users => {
                const userList = document.getElementById("businessList");
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

    function openChat(userName) {
        document.getElementById("chatArea").innerHTML = `<div class='text-gray-500 text-sm mb-2'>Chat with ${userName}</div>`;
        loadInquiries();
    }

    function loadInquiries() {
        fetch(`receive_inquiries.php?user_id=${CURRENT_USER_ID}`)
            .then(res => res.json())
            .then(data => {
                const chatArea = document.getElementById("chatArea");
                chatArea.innerHTML = "";

                let statusAdded = false;
                window.lastLoadedInquiry = null;

                data
                    .filter(inq =>
                        (inq.sender_id == CURRENT_USER_ID && inq.recipient_id == ACTIVE_RECIPIENT_ID) ||
                        (inq.recipient_id == CURRENT_USER_ID && inq.sender_id == ACTIVE_RECIPIENT_ID)
                    )
                    .forEach(inq => {
                        if (!window.lastLoadedInquiry) window.lastLoadedInquiry = inq;

                        const wrapper = document.createElement("div");
                        wrapper.className = (inq.sender_id == CURRENT_USER_ID) 
                            ? "ml-auto max-w-xs mb-2 text-right"
                            : "max-w-xs mb-2 text-left";

                        const bubble = document.createElement("div");
                        bubble.className = (inq.sender_id == CURRENT_USER_ID) 
                            ? "bg-[#67AB9F] text-white p-3 rounded-lg"
                            : "bg-white text-black p-3 rounded-lg";

                        bubble.textContent = inq.content;

                        const timestamp = document.createElement("div");
                        timestamp.className = "text-xs text-gray-500 mt-1";
                        timestamp.textContent = inq.sent_at;

                        wrapper.appendChild(bubble);
                        wrapper.appendChild(timestamp);

                        if (!statusAdded && inq.status) {
                            const statusText = document.createElement("div");
                            statusText.className = "text-sm text-gray-700 mt-2";
                            statusText.textContent = `Inquiry Status: ${inq.status}`;
                            wrapper.appendChild(statusText);

                            if (inq.status === "Pending" && CURRENT_USER_IS_BUSINESS) {
                                const buttonGroup = document.createElement("div");
                                buttonGroup.className = "flex gap-2 mt-2";

                                const approveBtn = document.createElement("button");
                                approveBtn.textContent = "Approve";
                                approveBtn.className = "bg-green-500 text-white px-3 py-1 rounded";
                                approveBtn.onclick = () => updateInquiryStatus(inq.inquiry_id, "Approved");

                                const rejectBtn = document.createElement("button");
                                rejectBtn.textContent = "Reject";
                                rejectBtn.className = "bg-red-500 text-white px-3 py-1 rounded";
                                rejectBtn.onclick = () => updateInquiryStatus(inq.inquiry_id, "Rejected");

                                buttonGroup.appendChild(approveBtn);
                                buttonGroup.appendChild(rejectBtn);
                                wrapper.appendChild(buttonGroup);
                            }

                            statusAdded = true;
                        }

                        chatArea.appendChild(wrapper);
                    });
            });
    }

    window.startInquiry = function () {
            const businessSelect = document.getElementById("sidebarBusinessSelect");
            const selectedOption = businessSelect.options[businessSelect.selectedIndex];

            const selectedBusinessId = selectedOption.value;
            const selectedServiceId = selectedOption.dataset.serviceId;
            const selectedBusinessName = selectedOption.text;

            if (!selectedBusinessId || !selectedServiceId) {
                alert("Please select a business to start an inquiry.");
                return;
            }

            ACTIVE_RECIPIENT_ID = selectedBusinessId;
            ACTIVE_SERVICE_ID = selectedServiceId;
            openChat(selectedBusinessName);
};

    window.toggleBusinessDropdown = function () {
        const dropdown = document.getElementById("businessDropdown");
        dropdown.classList.toggle("hidden");
        fetchAllBusinesses();
    }

    function fetchAllBusinesses() {
        fetch("get_all_businesses.php")
            .then(res => res.json())
            .then(businesses => {
                const businessSelect = document.getElementById("sidebarBusinessSelect");
                businessSelect.innerHTML = `<option disabled selected>Select a Business</option>`;

                businesses.forEach(business => {
                    let option = document.createElement("option");
                    option.value = business.owner_id; // this becomes the recipient user_id
                    option.dataset.serviceId = business.service_id; // 👈 stores service_id
                    option.textContent = business.business_name;
                    businessSelect.appendChild(option);
            });
        });
    }

    function updateInquiryStatus(inquiryId, newStatus) {
        fetch("update_inquiry_status.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ inquiry_id: inquiryId, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Inquiry " + newStatus.toLowerCase());
                loadInquiries();
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(err => alert("Something went wrong."));
    }
});
</script>
</body>
</html>
