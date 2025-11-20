// Client-Side Caching Layer with IndexedDB
class ClientCache {
    constructor() {
        this.dbName = 'bongdanet-cache';
        this.dbVersion = 1;
        this.db = null;
        this.initPromise = this.init();
    }

    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Create object store for API responses
                if (!db.objectStoreNames.contains('apiCache')) {
                    const apiStore = db.createObjectStore('apiCache', { keyPath: 'key' });
                    apiStore.createIndex('timestamp', 'timestamp', { unique: false });
                }
                
                // Create object store for match data
                if (!db.objectStoreNames.contains('matchData')) {
                    const matchStore = db.createObjectStore('matchData', { keyPath: 'matchId' });
                    matchStore.createIndex('timestamp', 'timestamp', { unique: false });
                }
            };
        });
    }

    async get(key, storeName = 'apiCache') {
        await this.initPromise;
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.get(key);

            request.onsuccess = () => {
                const result = request.result;
                if (result && this.isValid(result)) {
                    resolve(result.data);
                } else {
                    resolve(null);
                }
            };
            request.onerror = () => reject(request.error);
        });
    }

    async set(key, data, ttl = 60000, storeName = 'apiCache') {
        await this.initPromise;
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.put({
                key: key,
                data: data,
                timestamp: Date.now(),
                ttl: ttl
            });

            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    async getMatchData(matchId) {
        return this.get(matchId, 'matchData');
    }

    async setMatchData(matchId, data, ttl = 300000) {
        return this.set(matchId, data, ttl, 'matchData');
    }

    isValid(cachedItem) {
        if (!cachedItem || !cachedItem.timestamp) return false;
        const age = Date.now() - cachedItem.timestamp;
        return age < cachedItem.ttl;
    }

    async clear(storeName = 'apiCache') {
        await this.initPromise;
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.clear();

            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    async cleanup() {
        await this.initPromise;
        const stores = ['apiCache', 'matchData'];
        
        for (const storeName of stores) {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const index = store.index('timestamp');
            const cutoff = Date.now() - (24 * 60 * 60 * 1000); // 24 hours ago

            index.openCursor().onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    if (cursor.value.timestamp < cutoff) {
                        cursor.delete();
                    }
                    cursor.continue();
                }
            };
        }
    }
}

// Global cache instance
window.clientCache = new ClientCache();

// Cleanup old cache entries on load
if (typeof window !== 'undefined') {
    window.addEventListener('load', () => {
        setTimeout(() => clientCache.cleanup(), 5000);
    });
}

