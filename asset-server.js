import express from 'express';
import path from 'path';
import cors from 'cors';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 8080;

// Enable CORS for local development
app.use(cors({
    origin: ['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost'],
    credentials: true
}));

// Serve static files from Laravel's public directory
app.use('/assets', express.static(path.join(__dirname, 'public', 'assets'), {
    maxAge: '1d', // Cache for 1 day in development
    etag: true,
    lastModified: true
}));

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        server: 'Local Asset Server',
        port: PORT,
        timestamp: new Date().toISOString()
    });
});

// Serve index for root
app.get('/', (req, res) => {
    res.json({
        message: 'Local Asset Server for Laravel Development',
        endpoints: {
            assets: '/assets/*',
            health: '/health'
        },
        example: `http://localhost:${PORT}/assets/css/app.css`
    });
});

app.listen(PORT, () => {
    console.log(`🚀 Local Asset Server running on http://localhost:${PORT}`);
    console.log(`📁 Serving files from: ${path.join(__dirname, 'public', 'assets')}`);
    console.log(`🔗 Example: http://localhost:${PORT}/assets/css/app3.css`);
});

// Keep server running
process.on('SIGINT', () => {
    console.log('\n👋 Asset server shutting down...');
    process.exit(0);
});
