/**
 * Leadership Card Loader and Renderer
 * Dynamically loads and displays leadership cards from JSON data files
 */

(function() {
    'use strict';
    
    const LeadershipLoader = {
        /**
         * Loads leadership data from a JSON file
         * @param {string} year - The academic year (e.g., "2025-2026")
         * @returns {Promise<Object>} The leadership data
         */
        loadLeadershipData: async function(year) {
            try {
                const response = await fetch(`assets/data/leadership-${year}.json`);
                if (!response.ok) {
                    throw new Error(`Failed to load leadership data for ${year}`);
                }
                return await response.json();
            } catch (error) {
                console.error(`Error loading leadership data for ${year}:`, error);
                return null;
            }
        },
        
        /**
         * Renders a single leadership card
         * @param {Object} member - The member data
         * @returns {string} HTML string for the card
         */
        renderCard: function(member) {
            let avatarContent = '';
            
            if (member.image) {
                avatarContent = `<img src="${member.image}" alt="${this.escapeHtml(member.name)}" class="avatar-image">`;
            } else if (member.initials) {
                avatarContent = `<span class="avatar-initials">${this.escapeHtml(member.initials)}</span>`;
            } else {
                // Generate initials from name if not provided
                const initials = this.getInitials(member.name);
                avatarContent = `<span class="avatar-initials">${initials}</span>`;
            }
            
            return `
                <div class="leadership-card">
                    <div class="leadership-avatar">
                        ${avatarContent}
                    </div>
                    <h3 class="leadership-name">${this.escapeHtml(member.name)}</h3>
                    <p class="leadership-title">${this.escapeHtml(member.title)}</p>
                </div>
            `;
        },
        
        /**
         * Renders all leadership cards into a container
         * @param {string} containerId - The ID of the container element
         * @param {string} year - The academic year to load
         * @param {Object} options - Configuration options
         */
        renderLeadership: async function(containerId, year, options = {}) {
            const {
                showPreviousLink = false,
                heading = null,
                subtitle = null
            } = options;
            
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container with ID "${containerId}" not found`);
                return;
            }
            
            const data = await this.loadLeadershipData(year);
            if (!data) {
                container.innerHTML = '<p class="text-center text-muted">Unable to load leadership data.</p>';
                return;
            }
            
            // Clear container
            container.innerHTML = '';
            
            // Add heading and subtitle if provided
            if (heading || subtitle) {
                const headerDiv = document.createElement('div');
                headerDiv.className = 'text-center mb-8';
                
                if (heading) {
                    const headingEl = document.createElement('h2');
                    headingEl.textContent = heading;
                    headerDiv.appendChild(headingEl);
                }
                
                if (subtitle) {
                    const subtitleEl = document.createElement('p');
                    subtitleEl.className = 'text-secondary';
                    subtitleEl.textContent = subtitle;
                    headerDiv.appendChild(subtitleEl);
                }
                
                container.appendChild(headerDiv);
            }
            
            // Create grid container
            const grid = document.createElement('div');
            grid.className = 'leadership-grid';
            
            // Render all member cards
            data.members.forEach(member => {
                grid.insertAdjacentHTML('beforeend', this.renderCard(member));
            });
            
            // Add "Previous Board Members" link card if requested
            if (showPreviousLink) {
                grid.insertAdjacentHTML('beforeend', `
                    <div class="leadership-card leadership-card-link">
                        <div class="leadership-avatar">
                            <span class="avatar-icon"></span>
                        </div>
                        <h3 class="leadership-name">Previous Board Members</h3>
                        <p class="leadership-title">View Past Leadership</p>
                        <a href="previous-board.html" class="leadership-link">View Previous Boards</a>
                    </div>
                `);
            }
            
            container.appendChild(grid);
        },
        
        /**
         * Renders multiple years of leadership for the previous board page
         * @param {string} containerId - The ID of the container element
         * @param {Array<string>} years - Array of academic years to load
         */
        renderMultipleYears: async function(containerId, years) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Container with ID "${containerId}" not found`);
                return;
            }
            
            // Create year selector buttons
            const selectorContainer = document.createElement('div');
            selectorContainer.className = 'board-year-selector';
            
            years.forEach((year, index) => {
                const button = document.createElement('button');
                button.className = `year-button ${index === 0 ? 'active' : ''}`;
                button.setAttribute('data-year', year);
                button.textContent = `${year} Board`;
                selectorContainer.appendChild(button);
            });
            
            const selectorSection = document.createElement('section');
            selectorSection.className = 'section';
            selectorSection.innerHTML = `
                <div class="container">
                    <div class="text-center mb-8">
                        <h2>Select Academic Year</h2>
                        <p class="text-secondary">Choose a year to view the leadership team</p>
                    </div>
                </div>
            `;
            selectorSection.querySelector('.container').appendChild(selectorContainer);
            container.appendChild(selectorSection);
            
            // Create sections for each year
            const yearSections = [];
            for (const year of years) {
                const section = document.createElement('section');
                section.className = `section board-section ${year === years[0] ? 'active' : ''}`;
                section.id = `board-${year}`;
                
                const data = await this.loadLeadershipData(year);
                if (data) {
                    const innerContainer = document.createElement('div');
                    innerContainer.className = 'container';
                    
                    const headerDiv = document.createElement('div');
                    headerDiv.className = 'text-center mb-8';
                    headerDiv.innerHTML = `
                        <h2>${this.escapeHtml(data.academicYear)}</h2>
                        <p class="text-secondary">${year === years[0] ? 'Current' : 'Previous'} Leadership Team</p>
                    `;
                    innerContainer.appendChild(headerDiv);
                    
                    const grid = document.createElement('div');
                    grid.className = 'leadership-grid';
                    
                    data.members.forEach(member => {
                        grid.insertAdjacentHTML('beforeend', this.renderCard(member));
                    });
                    
                    innerContainer.appendChild(grid);
                    section.appendChild(innerContainer);
                }
                
                container.appendChild(section);
                yearSections.push(section);
            }
            
            // Add year selector functionality
            const buttons = selectorContainer.querySelectorAll('.year-button');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const year = this.getAttribute('data-year');
                    
                    // Remove active class from all buttons and sections
                    buttons.forEach(btn => btn.classList.remove('active'));
                    yearSections.forEach(section => section.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding section
                    this.classList.add('active');
                    document.getElementById(`board-${year}`).classList.add('active');
                });
            });
        },
        
        /**
         * Gets the most recent leadership year
         * @param {Array<string>} years - Array of available years
         * @returns {string} The most recent year
         */
        getMostRecentYear: function(years) {
            return years.sort().reverse()[0];
        },
        
        /**
         * Generates initials from a name
         * @param {string} name - The full name
         * @returns {string} The initials
         */
        getInitials: function(name) {
            return name
                .split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 3);
        },
        
        /**
         * Escapes HTML to prevent XSS
         * @param {string} text - The text to escape
         * @returns {string} Escaped text
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    
    // Expose globally
    window.LeadershipLoader = LeadershipLoader;
})();

