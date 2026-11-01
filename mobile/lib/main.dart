import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'screens/search/coin_search_screen.dart';
import 'screens/scanner/coin_scanner_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ),
  );
  runApp(const NumismaticApp());
}

class NumismaticApp extends StatelessWidget {
  const NumismaticApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Antik Nümizmatik Ansiklopedisi',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: const Color(0xFF090D16),
        primaryColor: Colors.amber,
        colorScheme: const ColorScheme.dark(
          primary: Colors.amber,
          secondary: Colors.amberAccent,
          surface: Color(0xFF0F172A),
        ),
        useMaterial3: true,
      ),
      home: const MainNavigationScreen(),
    );
  }
}

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    const CoinSearchScreen(),
    const CoinScannerScreen(),
    const Center(
      child: Text(
        'Kişisel Koleksiyon Defteri (Sanctum Sync)',
        style: TextStyle(color: Colors.white70),
      ),
    ),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        backgroundColor: const Color(0xFF0F172A),
        indicatorColor: Colors.amber.withOpacity(0.2),
        onDestinationSelected: (index) {
          setState(() => _currentIndex = index);
        },
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.menu_book_outlined, color: Colors.grey),
            selectedIcon: Icon(Icons.menu_book, color: Colors.amber),
            label: 'Katalog',
          ),
          NavigationDestination(
            icon: Icon(Icons.camera_alt_outlined, color: Colors.grey),
            selectedIcon: Icon(Icons.camera_alt, color: Colors.amber),
            label: 'Tara',
          ),
          NavigationDestination(
            icon: Icon(Icons.bookmark_border, color: Colors.grey),
            selectedIcon: Icon(Icons.bookmark, color: Colors.amber),
            label: 'Koleksiyonum',
          ),
        ],
      ),
    );
  }
}

