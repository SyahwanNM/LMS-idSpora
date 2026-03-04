<nav id="sidebar">
    <button id="toggle-btn" type="button" aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
            fill="#e3e3e3">
            <path
                d="m242-200 200-280-200-280h98l200 280-200 280h-98Zm238 0 200-280-200-280h98l200 280-200 280h-98Z" />
        </svg>
    </button>
    <ul>
        <li class="{{ ($activeMenu ?? '') === 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('trainer.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="{{ ($activeMenu ?? '') === 'events' ? 'active' : '' }}">
            <a href="{{ route('trainer.events') }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="M580-240q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z" />
                </svg>
                <span>Event</span>
            </a>
        </li>
        <li class="{{ ($activeMenu ?? '') === 'course' ? 'active' : '' }}">
            <a href="{{ route('trainer.courses') }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="m460-380 280-180-280-180v360ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320Zm0-80h480v-480H320v480ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                </svg>
                <span>Course</span>
            </a>
        </li>
        <li class="{{ ($activeMenu ?? '') === 'feedback' ? 'active' : '' }}">
            <a href="{{ route('trainer.feedback') }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="M80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z" />
                </svg>
                <span>Feedback</span>
            </a>
        </li>
    </ul>
    <div class="profile">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e3e3e3">
                    <path
                        d="M186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840H488v66.67H186.67v586.66H488V-120H186.67Zm434.66-176.67-47-47L660-430H360v-66.67h300l-85.67-85.66 47-47L786.67-464 621.33-296.67Z" />
                </svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</nav>