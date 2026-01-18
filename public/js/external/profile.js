const posts = [
            {
                id: 1,
                author: 'University Admin',
                time: '2 hours ago',
                content: 'Reminder: Annual Sports Day is scheduled for next Saturday! All external members are welcome to participate. Registration closes tomorrow.',
                likes: 45,
                comments: []
            },
            {
                id: 2,
                author: 'Faculty of Computing',
                time: '1 day ago',
                content: 'Exciting news! We\'re hosting a Tech Talk series next month featuring industry experts. External members can register through the portal.',
                likes: 78,
                comments: [
                    { author: 'John Doe', text: 'This sounds great! How do I register?' },
                    { author: 'Admin', text: 'Registration link will be shared soon!' }
                ]
            },
            {
                id: 3,
                author: 'Student Council',
                time: '3 days ago',
                content: 'Thank you to all external members who participated in our recent community outreach program. Your support made a huge difference!',
                likes: 92,
                comments: []
            }
        ];

        function renderPosts() {
            const container = document.getElementById('postsContainer');
            container.innerHTML = posts.map(post => `
                <div class="post-card">
                    <div class="post-header">
                        <div class="post-avatar">👨‍💼</div>
                        <div class="post-info">
                            <h4>${post.author}</h4>
                            <div class="post-time">${post.time}</div>
                        </div>
                    </div>
                    <div class="post-text">${post.content}</div>
                    <div class="post-actions">
                        <button class="action-button" onclick="likePost(${post.id})">
                            👍 Like (${post.likes})
                        </button>
                        <button class="action-button" onclick="toggleComments(${post.id})">
                            💬 Comment (${post.comments.length})
                        </button>
                    </div>
                    <div class="comments-section" id="comments-${post.id}" style="display: ${post.comments.length > 0 ? 'block' : 'none'};">
                        <div class="comment-input-box">
                            <input type="text" placeholder="Write a comment..." id="input-${post.id}">
                            <button onclick="addComment(${post.id})">Post</button>
                        </div>
                        <div id="comment-list-${post.id}">
                            ${post.comments.map(c => `
                                <div class="comment">
                                    <div class="comment-avatar">👤</div>
                                    <div class="comment-body">
                                        <div class="comment-author">${c.author}</div>
                                        <div class="comment-text">${c.text}</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById(tabName + 'Content').classList.add('active');
        }

        function toggleComments(postId) {
            const section = document.getElementById(`comments-${postId}`);
            section.style.display = section.style.display === 'none' ? 'block' : 'none';
        }

        function addComment(postId) {
            const input = document.getElementById(`input-${postId}`);
            if (input.value.trim()) {
                const post = posts.find(p => p.id === postId);
                post.comments.push({ author: 'You', text: input.value.trim() });
                renderPosts();
            }
        }

        function likePost(postId) {
            const post = posts.find(p => p.id === postId);
            post.likes++;
            renderPosts();
        }

        function editProfile() {
            alert('Edit Profile functionality - This will open an edit form');
        }

        renderPosts();