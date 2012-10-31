
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileReader;
import java.io.FileWriter;
import java.util.ArrayList;
import java.util.HashMap;

public class Kereso {

    public static HashMap<String, ArrayList<Integer>> words;
    public static int[][] matrix;
    public static String[] sorok;
    public static double[] rank;
    public static int[] Nj;
    public static int n;

    public static void main(String[] args) {



        beolvas();
    }

    public static void beolvas() {
        try {
            BufferedReader be;
            be = new BufferedReader(new FileReader("top_depth.txt"));
            String line;
            n = Integer.parseInt(be.readLine());
            matrix = new int[n][n];
            for (int i = 0; i < n; i++) {
                for (int j = 0; j < n; j++) {
                    matrix[i][j] = 0;
                }
            }
            sorok = new String[n];

            int i = 0;
            while ((line = be.readLine()) != null && i++ < n) {
                String[] data = line.split(" ");
                int id = Integer.parseInt(data[0]);
                sorok[id - 1] = data[1];
            }
            int cnt = Integer.parseInt(line);
            while ((line = be.readLine()) != null && i++ < cnt) {
                String[] data = line.split(" ");
                int node1id = Integer.parseInt(data[0]) - 1;
                int node2id = Integer.parseInt(data[1]) - 1;
                matrix[node1id][node2id] = 1;
            }
            be.close();
        } catch (Exception e) {
            System.out.println("Hiba" + e.toString());
        }

        words = new HashMap<String, ArrayList<Integer>>();

        try {
            BufferedReader be;
            be = new BufferedReader(new FileReader("index_depth.txt"));
            String line;
            while ((line = be.readLine()) != null) {
                String[] data = line.split(" ");
                ArrayList<Integer> sites = new ArrayList<Integer>();
                for (int j = 1; j < data.length; j++) {
                    sites.add(Integer.parseInt(data[j]) - 1);
                }

                words.put(data[0], sites);


            }
            be.close();
        } catch (Exception e) {
            System.out.println("Hiba" + e.toString());
        }

        Nj = new int[n];
        rank = new double[n];

        int count = 0;
        for (int i = 0; i < n; i++) {
            count = 0;
            for (int j = 0; j < n; j++) {
                count += matrix[i][j];
            }
            Nj[i] = count;
        }


        double defaultRank = 1.0 / n;
        for (int i = 0; i < n; i++) {
            rank[i] = defaultRank;
        }

        for (int j = 0; j < 100; j++) {
            double[] rank2 = new double[n];
            for (int i = 0; i < n; i++) {
                rank2[i] = PageRank(i);
                //System.out.println((i + 1) + ":" + Nj[i] + ":" + rank[i] + " | " + rank2[i]);
            }
            rank = rank2;
        }

        for (int i = 0; i < n; i++) {
            System.out.println((i + 1) + ":" + Nj[i] + ":" + String.format("%1$.9f", rank[i]));
        }

        try {
            // Create file 
            FileWriter fstream = new FileWriter("ranks.txt");
            BufferedWriter out = new BufferedWriter(fstream);
            for (int i = 0; i < n; i++) {
                out.write((i + 1) + " " + sorok[i] +" " + String.format("%1$.9f", rank[i]) + "\n");
            }
            //Close the output stream
            out.close();
        } catch (Exception e) {//Catch exception if any
            System.err.println("Error: " + e.getMessage());
        }

    }

    public static double PageRank(int ind) {


        /*
	   
         if(depth == 0) {
         return 1.0/n;
         }
         * 
         * */

        /*
         int next=0;
         double prevPR = 0;
       
         for(int i=0; i<cnt; i++) {
         while(next<n){		//backlist elemeinek megkeresése, összesen cnt db
         if(matrix[next][ind] == 1) {
         PR += PageRank(next, depth-1) / Nj[next];   				   
         }
         next++;
         break;
         }
         }
       
         */

        double PR = 0;
        for (int i = 0; i < n; i++) {
            if (matrix[i][ind] == 1) {
                PR += rank[i] / Nj[i];
            }
        }
        PR *= 0.88;
        PR += 0.12 / n;


        return PR;
    }
}